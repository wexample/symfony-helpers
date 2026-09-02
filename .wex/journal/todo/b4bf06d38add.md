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
et `HasSecureIdTrait` comme le point de blocage puisqu'il en faisait une colonne remplie
par génération aléatoire. La migration UUIDv7 a supprimé tout cela : `AbstractEntity`
porte `protected Uuid $id` généré dans le constructeur, `AbstractEntityInterface` expose
`getId(): Uuid` / `setId(Uuid)`, et il ne reste pas une occurrence de `secureId` dans
`symfony-helpers/src`.

Une entité non-Doctrine a donc déjà une identité utilisable, à condition de la **dériver
de sa clé naturelle** au lieu de la laisser se regénérer à chaque lecture —
`Uuid::v5($namespace, $cléNaturelle)`.

### 2. Le repository — **réglé aussi, et sans quitter `find()`**

`AbstractEntityService::getEntityRepository()` retourne maintenant
`Doctrine\Persistence\ObjectRepository` au lieu d'`AbstractRepository`. Élargissement non
cassant (la covariance de retour autorise les enfants à restreindre) ;
`WithMessageEntityServiceTrait` a reçu le `@var AbstractRepository` que son appel à
`queryForEntitiesIds()` réclamait.

Le contrat `ObjectRepository` est exactement ce qu'il fallait : `find`, `findOneBy`,
`findBy($criteria, $orderBy, $limit, $offset)`, `findAll`, `getClassName`. Le `limit` /
`offset` y est **nativement**, ce qui répond à la contrainte de pagination.

Reste attaché à Doctrine : `EntityNeutralService` exige un `EntityManagerInterface` en
constructeur. Inerte tant qu'on ne s'en sert pas (autowiring), à traiter si ça gêne.

## Décision prise : on adresse par chemin, pas par uuid

L'id est un `Uuid::v5` du chemin, donc à sens unique : on ne remonte pas au fichier
depuis l'uuid sans balayer l'arbre. Options écartées : un index uuid → chemin (échoue sur
un deep-link à froid), un id réversible (un chemin ne tient pas dans 128 bits).

Donc **le chemin est l'identifiant de lookup, l'uuid est l'identité aval** (clés de
liste, diffing, relations dans le normalizer). `find($path)` marche, `find($uuid)` lève
`UnexpectedValueException` avec un message explicite plutôt que de rendre `null` en
silence. Conséquence à assumer : les routes de ce package n'adressent pas par id comme
partout ailleurs.

## Fait

- `symfony-helpers` — `AbstractEntityService::getEntityRepository(): ObjectRepository`.
- `symfony-file` `src/Entity/FileSystemItemEntity` — porte `$path` et un
  `FileSystemItemType`, dérive son id par `Uuid::v5(ID_NAMESPACE, $path)` au lieu
  d'appeler `parent::__construct()`.
- `symfony-file` `src/Enum/FileSystemItemType` — `FILE`, `DIRECTORY`, `LINK`, avec
  `fromPath()`.
- `symfony-file` `src/Repository/FileSystemItemRepository implements ObjectRepository` —
  critères `path` et `parent`, `findBy` répond **un niveau à la fois** et jamais l'arbre,
  `findAll()` = le niveau racine.

**Une seule classe d'entité, ni abstraite ni scindée en Fichier / Dossier.** La
divergence qu'on imagine entre les deux (« un dossier a des enfants ») appartient au
repository, pas à l'entité : c'est déjà `findBy(['parent' => …])`. Une fois ça retiré il
ne reste rien à spécialiser, un listing est de toute façon une collection mixte que deux
classes obligeraient à recomposer côté front, et le couple fichier/dossier est faux dès
qu'on regarde — les liens symboliques existent, POSIX en a d'autres. Prix assumé : les
champs propres aux fichiers (taille, mime) seront `null` sur un dossier. Repasser à deux
classes plus tard est plus facile que l'inverse.

Les chemins circulent **relatifs à la racine** : l'uuid reste stable d'une machine à
l'autre et le front ne voit jamais un chemin serveur absolu. `toContainedAbsolutePath()`
résout liens et `..` puis rend `null` pour tout ce qui atterrit hors racine — un chemin
qui s'échappe est indistinguable d'un chemin absent, ce qui est le comportement voulu
pour une route.

Vérifié en conteneur sur le package lui-même : `findAll` liste la racine, `findBy(parent:
src)` un niveau, `limit`/`offset` découpent, `find("composer.json")` rend l'entité et son
uuid, `find("nope.txt")` et `find("../../../../etc/passwd")` rendent `null`, `find($uuid)`
et un critère inconnu lèvent.

## Suite — passe la main à l'agent `file`

L'étape qui donne la vraie liste de travail reste à faire : faire passer une entité
fichier concrète dans **normalizer → DTO → réponse JSON d'API**. Ce qui accroche là est à
traiter ; ce qui passe n'a pas besoin d'être refactoré.

Points d'attention repérés :

- `AbstractEntityNormalizer` type contre la **classe** `AbstractEntity`, pas contre
  l'interface. Tant que l'entité fichier en hérite, ça passe ; le retypage vers
  l'interface n'est à faire que si on veut une base sans mapping Doctrine. Sous-classes
  du normalizer dans le stack : `symfony-money` `DefaultCurrencyNormalizer`,
  `symfony-api` `ApiNormalizerRule` (à revérifier).
- `symfony-api` `ApiDtoRepositoryTrait` type `AbstractEntity` en retour.
- Le normalizer re-normalise chaque relation (`collectRelationshipsFromEntityData`) : un
  dossier de 5000 entrées ne peut pas être une collection de relations. Vérifier qu'il
  supporte de ne pas voir les enfants d'un objet.
- Les attributs Doctrine hérités d'`AbstractEntity` sont **inertes** tant que la classe
  concrète n'est pas `#[ORM\Entity]` : Doctrine ne la voit jamais. Pas besoin d'une base
  sœur non mappée avant d'en avoir la preuve du contraire.
- `AbstractRepository::getDefaultIdentifierName()` existe déjà dans `symfony-helpers` et
  vaut `'id'`, surchargeable — le crochet exact pour « adresser par chemin ». Personne ne
  l'appelle aujourd'hui dans tout le stack : à câbler ou à supprimer.

## Ouvert

- Nom et emplacement d'une éventuelle base non persistée, si l'étape normalizer prouve
  qu'elle est nécessaire.
- Entités concrètes Fichier / Dossier, et leur normalizer : c'est le travail de l'agent
  `file`. Attention au nom : `symfony-filestate` existe déjà et parle d'autre chose.
