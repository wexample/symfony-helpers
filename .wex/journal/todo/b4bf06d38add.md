# Entités et repositories sans Doctrine

Opened: 2026-09-02
Updated: 2026-09-02
Author: agent:addon:ai/editor (depuis app-board)

# Entités et repositories sans Doctrine

## Mission

Permettre à des entités dont la source n'est **pas une base de données** de traverser
tout l'écosystème wexample — normalizers, DTO, routes d'API, exports, sérialisation —
exactement comme les entités Doctrine le font aujourd'hui.

Cas déclencheur : le système de fichiers, pour `app-board`. Cible réelle : « entité dont
la source n'est pas Doctrine » (YAML, API distante, calcul suivront).

## Le couplage, réévalué après la migration UUIDv7

### 1. L'identité — **réglé**

L'analyse d'origine désignait `getSecureId()` comme le vrai porteur d'identité en aval,
et `HasSecureIdTrait` comme le point de blocage, puisqu'il en faisait une colonne remplie
par génération aléatoire.

La migration UUIDv7 a supprimé tout cela. `AbstractEntity` porte maintenant
`protected Uuid $id`, généré dans le constructeur (`Uuid::v7()`), pas par la base.
`AbstractEntityInterface` expose `getId(): Uuid` / `setId(Uuid)`. Plus une seule
occurrence de `secureId` dans `symfony-helpers/src`.

Conséquence : une entité non-Doctrine a déjà une identité utilisable, à condition de la
**dériver de sa clé naturelle** au lieu de la laisser se regénérer à chaque lecture.
`Uuid::v5($namespace, $path)` fait cela.

### 2. Le repository — **fait, côté service**

`AbstractEntityService::getEntityRepository()` retournait `AbstractRepository`
(Doctrine). Il retourne maintenant `Doctrine\Persistence\ObjectRepository` : le contrat
standard (`find`, `findOneBy`, `findBy`, `findAll`, `getClassName`), que tout repository
Doctrine satisfait déjà par héritage et qu'une source non-Doctrine peut implémenter
directement. Élargissement non cassant (la covariance de retour autorise les enfants à
restreindre) ; `WithMessageEntityServiceTrait` a reçu le `@var AbstractRepository` que
son appel à `queryForEntitiesIds()` réclamait.

Reste attaché à Doctrine : `EntityNeutralService` exige un `EntityManagerInterface` en
constructeur. Inerte tant qu'on ne s'en sert pas (autowiring), à traiter si ça gêne.

## Fait

- `symfony-helpers` — `AbstractEntityService::getEntityRepository(): ObjectRepository`.
- `symfony-file` — `src/Entity/AbstractFileSystemItemEntity`, première classe réelle du
  package : porte `$path`, dérive son id par `Uuid::v5(ID_NAMESPACE, $path)` au lieu
  d'appeler `parent::__construct()`. Vérifié en conteneur : même chemin → même id,
  chemins différents → ids différents, `getEntityShortName()` répond.

## Suite — passe la main à l'agent `file`

L'étape 1 de l'ordre d'exécution reste à faire, et c'est elle qui donne la vraie liste de
travail : faire passer une entité fichier concrète dans **normalizer → DTO → réponse JSON
d'API**. Ce qui accroche là est à traiter ; ce qui passe n'a pas besoin d'être refactoré.

Points d'attention déjà repérés :

- `AbstractEntityNormalizer` type contre la **classe** `AbstractEntity`, pas contre
  l'interface. Tant que l'entité fichier hérite d'`AbstractEntity`, ça passe ; le
  retypage vers l'interface n'est à faire que si on veut une base sans mapping Doctrine.
  Sous-classes du normalizer dans le stack : `symfony-money`
  `DefaultCurrencyNormalizer`, `symfony-api` `ApiNormalizerRule` (à revérifier).
- `symfony-api` `ApiDtoRepositoryTrait` type `AbstractEntity` en retour.
- **Pagination obligatoire** : un dossier contient `node_modules`, `vendor`, `.git`. Le
  repository doit répondre un niveau à la fois avec limite et offset, jamais construire
  l'arbre. Le normalizer re-normalise chaque relation
  (`collectRelationshipsFromEntityData`) — 5000 entrées ne peuvent pas être une
  collection de relations.
- Les attributs Doctrine hérités d'`AbstractEntity` sont **inertes** tant que la classe
  concrète n'est pas `#[ORM\Entity]` : Doctrine ne la voit jamais. Pas besoin d'une base
  sœur non mappée avant d'en avoir la preuve du contraire.
- `ID_NAMESPACE` est fixe et le chemin est haché tel quel : à la charge de
  l'implémentation concrète de décider si ce chemin est absolu (id stable par machine)
  ou relatif à une racine (id stable partout).

## Ouvert

- Nom et emplacement d'une éventuelle base non persistée, si l'étape 1 prouve qu'elle est
  nécessaire.
- Le package filesystem lui-même (entités fichier/dossier, repository qui liste un
  niveau, normalizer) reste hors périmètre ici. Attention au nom : `symfony-filestate`
  existe déjà et parle d'autre chose.
