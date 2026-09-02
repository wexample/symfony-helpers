# Entités et repositories sans Doctrine

Opened: 2026-09-02
Updated: 2026-09-02
Author: agent:addon:ai/editor (depuis app-board)

# Entités et repositories sans Doctrine

## Mission

Permettre à des entités dont la source n'est **pas une base de données** de traverser
tout l'écosystème wexample — normalizers, DTO, routes d'API, exports, sérialisation —
exactement comme les entités Doctrine le font aujourd'hui.

Le cas qui déclenche la demande : un système de fichiers. `app-board` doit afficher la
liste des fichiers d'une app managée, et plein d'autres apps auront le même besoin. Le
filesystem *est* déjà un store — il a des objets, une identité stable et une façon de
les interroger. Ce qui lui manque, c'est le droit d'entrer dans la chaîne
`entity → normalizer → dto → json → front` sans qu'on réinvente à côté des normes
d'API, de routes CRUD et de sérialisation parallèles.

D'autres sources suivront le même chemin : configuration YAML, API distante, calcul.
La cible n'est donc pas « le filesystem », c'est **« entité dont la source n'est pas
Doctrine »**.

## Ce que le couplage à Doctrine est réellement

Relevé fait en lisant le package. Il est plus petit qu'il n'en a l'air : ce qui couple
n'est pas la persistance, ce sont **deux points précis**.

### 1. L'identité

`Entity/Interfaces/AbstractEntityInterface.php` ne demande que deux méthodes
(`getId(): ?int`, `setId(int)`), et le reste de l'écosystème type déjà contre elle :
`Service/Entity/AbstractEntityService`, `Helper/EntityHelper`,
`Service/AbstractEntitySearchService`, `Entity/Traits/LinkedToEntityTrait`. Ce n'est
pas là qu'est le problème.

Le vrai porteur d'identité en aval, c'est `getSecureId()` :

- `Normalizer/AbstractEntityNormalizer` référence **toutes** ses relations par secure
  id (`extractRelationshipsFromValue`, `buildIdValue`) ;
- `symfony-api` `Api/Dto/AbstractEntityDto` a un `$secureId` et un
  `fromEntity(AbstractEntity)` ;
- les routes d'API adressent les objets par là.

Or `Entity/Traits/HasSecureIdTrait` en fait une **colonne** :
`#[Column(type: string, length: 255)] protected string $secureId`, remplie par une
génération aléatoire (`setGeneratedSecureId`). Une entité non-Doctrine a déjà une
identité stable — le chemin, pour un fichier — mais aucun moyen de la fournir
elle-même.

→ Extraire le contrat (`getSecureId(): string`) du trait vers une interface, et
retyper `AbstractEntityNormalizer` contre cette interface au lieu de
`AbstractEntity`.

**Coût mesuré du retypage.** PHP interdit de restreindre un type de paramètre dans une
classe fille : si le parent élargit vers une interface, les enfants doivent élargir
aussi. Sous-classes de `AbstractEntityNormalizer` dans tout le stack au moment où
c'est écrit : **deux** — `symfony-money/src/Api/Normalizer/Entity/Currency/DefaultCurrencyNormalizer.php`
et `symfony-api/src/Service/Rectify/Rule/ApiNormalizerRule.php`. À revérifier avant de
commencer, mais l'ordre de grandeur est là.

Question ouverte : garder le nom `secureId` pour quelque chose qui n'a plus rien de
secret ni de généré (le chemin d'un fichier), ou introduire un nom neutre dont
`secureId` devient un cas ? Le renommage a un coût bien plus élevé que l'extraction
d'interface — il touche le format JSON envoyé au front.

### 2. Le repository

`Repository/AbstractRepository extends ServiceEntityRepository` : rien n'y est
récupérable pour une source non-Doctrine (QueryBuilder, EntityManager, flush).

Le contrat standard existe déjà et ne coûte rien à implémenter :
`Doctrine\Persistence\ObjectRepository` (`find`, `findOneBy`, `findBy`, `findAll`,
`getClassName`). Tous les repositories Doctrine le satisfont déjà par héritage.

→ Le code générique (API, DTO, services d'entités) doit typer contre ce contrat, pas
contre `AbstractRepository`. Une source non-Doctrine l'implémente directement.

Point d'attention repéré : `symfony-api/src/Repository/Traits/ApiDtoRepositoryTrait.php`
type contre `AbstractEntity` en retour — à vérifier au passage, il est dans un autre
package mais sur le même chemin.

## Contrainte de conception à ne pas rater

Une source non-Doctrine peut être **énorme et non paginée par nature** : un dossier
comme `/apps/<name>` contient `node_modules`, `vendor`, `.git`. Le repository doit
pouvoir répondre **un niveau à la fois** avec limite et offset, jamais construire
l'arbre complet.

Conséquence sur le normalizer : sa cascade de relations re-normalise chaque objet lié
(`collectRelationshipsFromEntityData`). Un dossier de 5000 entrées ne peut pas être une
collection de relations. Il faut donc que `findBy(..., limit)` soit dans le contrat
attendu, et vérifier que le normalizer supporte de ne pas voir les enfants d'un objet.

## Ordre d'exécution recommandé

Ne pas commencer par modifier les classes de base. L'étape zéro prouve la chaîne
complète sans toucher une ligne du package :

1. **Preuve** — une entité de test qui étend `AbstractEntity` **sans mapping Doctrine**
   (pas de `#[ORM\Entity]`, donc Doctrine ne la voit jamais) avec `getSecureId()`
   surchargé pour renvoyer une identité maison. Faire passer dessus : normalizer → DTO
   → réponse JSON d'API. Ce qui accroche là est la vraie liste de travail ; ce qui
   passe n'a pas besoin d'être refactoré.
2. **Extraction** — l'interface d'identité, le retypage du normalizer, le typage du
   code générique contre `ObjectRepository`.
3. **Base propre** — une classe de base sœur d'`AbstractEntity` sans mapping ni `$id`
   entier, une fois qu'on sait de quoi elle a besoin. Le `$id` int hérité et jamais
   rempli de l'étape 1 est exactement ce que cette étape nettoie.

## Hors périmètre

Le package filesystem lui-même (entités fichier/dossier, repository qui liste un
niveau, normalizer) n'est pas ici : il est le premier **client** de ce travail, il
viendra après et probablement dans un package à part. Attention au nom :
`wexample/symfony-filestate` existe déjà et parle d'autre chose (règles de
rectification interrogées par le package Python).

## État

Rien de commencé. Analyse faite depuis `app-board`, à valider par lecture directe du
code avant d'écrire.

## Ouvert

- Nom du contrat d'identité, et faut-il en profiter pour sortir du mot `secureId`.
- Est-ce que `AbstractEntityInterface` (`getId(): ?int`) garde un sens pour une source
  sans identifiant entier, ou est-ce qu'il doit se scinder.
- Où vit la classe de base non-persistée : dans `symfony-helpers` à côté
  d'`AbstractEntity`, ou dans un package dédié.
