# Dissoudre symfony-helpers et php-helpers en packages par responsabilité

Opened: 2026-10-05
Updated: 2026-10-05
Author: agent:main (avec weeger)

## Pourquoi

Une micro-modif dans `symfony-helpers` republie la stack : 49 des 67 packages en
dépendent, et toute modif de signature lève un majeur (on ne sait pas détecter un
changement non cassant au-delà du superficiel). Or 57 des 60 derniers commits de code
ne touchent pas le noyau universel (`Class\AbstractBundle`, `DependencyInjection`),
et 38 sur 60 ne touchent qu'un seul sous-dossier.

Un split par dossier `src/` ne suffit pas (31 % de cascade en moins, médiane toujours
à 47 packages) : `Helper` est au fond de l'empilement et 10 dossiers en dépendent.
Le principe retenu : **plus de package « helpers »**, chaque fonctionnalité rattachée
à sa responsabilité. Créer un package ne coûte rien ; c'est la séparation qui compte.

Le noyau va dans un `symfony-bundle` qui ne bouge quasiment jamais : s'il bouge, on
republie tout, mais ce n'est pas lui qui bouge souvent.

## Couche Symfony — destinations de symfony-helpers

Entre parenthèses : nombre de packages dépendants dans la suite.

- [ ] **symfony-bundle** *(nouveau, socle immobile)* (47) — `AbstractBundle`,
  `AbstractWexampleSymfonyExtension`, Bundle et Extension de helpers renommés,
  `BundleClassTrait`, `BundleHelper`, `PackageHelper`, `BundleService`,
  `LoaderBundleInterface`, `AbstractController`, `AbstractCommand`,
  `AbstractBundleCommand`, `Command\Traits\*` (sauf entité), `Twig\AbstractExtension`,
  `HasEnvKeysTrait`, `HasParameterBagEnvKeysTrait`, `MissingRequiredEnvVarError`,
  `EnvironmentHelper`, `ConsoleLoggerTrait`, `CommandLoggerTrait`,
  `Validator\Constraints\File`
- [ ] **symfony-entity** *(nouveau)* (22) — `AbstractEntity` et interfaces,
  `BaseEntityTrait`, tous les `Has*Trait` utilisés, `LinkedToEntityTrait`,
  `EntityManipulatorTrait`, `AbstractRepository`, `DateRepositoryTrait`,
  `AbstractEntityService`, `EntityNeutralService`, `WithMessageEntityServiceTrait`,
  `EntityHelper`, `AbstractEntityController`, `EntityControllerTrait`,
  `EntityManipulationCommandTrait`, `LinkableEntity`, `ImportableEntity`, `EntityDto`,
  `WithDataMigrationTrait`, `WithTableNameTrait`, `ServiceSelectorTrait`
- [ ] **symfony-normalizer** *(nouveau)* (7) — les 3 `Normalizer`,
  `NormalizableDataInterface` (10 commits sur 60, 7 utilisateurs seulement)
- [ ] **symfony-security** *(existe)* — `RoleHelper`, `ReversedRoleHierarchy`,
  `Voter\*`, `UserEntityInterface`, `WithUserEntityInterface`, `LinkedToUserTrait`,
  `AccessControlledRepositoryTrait`
- [ ] **symfony-routing** *(existe)* — `Routing\*`, `RouteHelper`,
  `SimpleRoutesController`, `SimpleMethodResolver`, `HasSimpleRoutesControllerTrait`,
  `RequestHelper` (partie Symfony), `TypesHelper`
- [ ] **symfony-api** *(existe)* — `DateQueryStringConstraint` et
  `MultipleTypeConstraint` avec leurs validators, `ViolationHelper`, `DebugHelper`,
  `DataHelper`, `AbstractException`, `NotAllowedItemExceptionTrait`,
  `RenderableResponse` et ses `ResponseRenderProcessor`
- [ ] **symfony-template** *(existe)* — `HeadLinkProviderInterface`,
  `HeadMetaProviderInterface`, `WithBodyClassTrait`, `LoremIpsumExtension`
- [ ] **symfony-dev** *(existe)* — `Service\Syntax\*`, `AbstractCheckNodeInstallCommand`
- [ ] **symfony-coding** *(existe)* — `GitRepositoryReader`
- [ ] **symfony-search** *(existe)* — `AbstractEntitySearchService`,
  `SearchableRepositoryTrait`
- [ ] **symfony-ai** *(existe)* — `VectorType`, `HasEmbeddingTrait`
- [ ] **symfony-platform** *(existe)* — `SystemParameter`, son repository, son service,
  son manipulator
- [ ] **symfony-company** *(existe)* — `Organization`, `OrganizationType`
- [ ] **symfony-money** *(existe)* — `PriceHelper`
- [ ] **symfony-mail** / **symfony-forms** *(existent)* — `MailHelper`,
  `NotifierHelper` / `FormHelper`
- [ ] **dissoudre `VariableHelper`** — sac de constantes chaînes : chaque constante
  retourne chez son consommateur (11 packages, **228 usages dans les apps**)

## Couche PHP pur — destinations de php-helpers et du reste de symfony-helpers

- [ ] **php-string** — casse (`toCamel`, `toKebab`, `toSnake`, `toClass`,
  `camelToDash`, `kebabToDash`, `stringToKebab`), préfixes / suffixes / chunks
  (`removePrefix`, `removeSuffix`, `trim*Chunk*`, `getFirstChunk`, `getLastChunk`,
  `inlineSubString`), nettoyage (`slugify`, `removeAccents`, `toAlphaNum`,
  `alphanumericOnly`, `removeDoubleSpaces`, `removeNewLines`, `splitLines`,
  `createAbstract`, `trimString`), `toString`, `toList`, `hasOne`
- [ ] **php-number** — tout `NumberHelper` (arrondis, `isIntegerLike`, `intData` ↔ float,
  chiffres romains — à supprimer s'ils restent inutilisés), `getFloatFromString`,
  `getIntDataFromString`, `getStringFromIntData`
- [ ] **php-boolean** — `parseBoolean`, `parseBooleanOrNull`, `isBoolOrBoolString`,
  `isBooleanOrNull`, `isNullOrNullString`, `renderBoolean`
- [ ] **php-array** — les deux `ArrayHelper` fusionnés, `sortOn` (sorti de `ClassHelper`)
- [ ] **php-class** — moitié nommage de `ClassHelper` (`getShortName`, `getKebabName`,
  `getTableizedName`, `longTableized*`, `splitNamespace`, `buildClassNameFromPath`…),
  `HasShortClassNameClassTrait`, `HasSnakeShortClassNameClassTrait`, `HasUniqueId`
- [ ] **php-reflection** — moitié réflexion de `ClassHelper` (attributs, traits,
  interfaces, `callMethodIfExists`, getters / setters de champs,
  `applyPropertiesSetters`), `AbstractFromArrayObject`
- [ ] **php-file** *(existe)* — les deux `FileHelper` fusionnés, `DirHelper`,
  `PathHelper`, `uniqueFileName*`, `objectToFileName`, `trimExtension`, `formatBytes`,
  `convertToBytes`
- [ ] **php-json** *(nouveau, miroir de php-yaml)* — `JsonHelper`
- [ ] **php-yaml** *(existe)* — `WithYamlTestCase`
- [ ] **php-crypto** — `encrypt`, `decrypt`, `generateSecureId`, `generatePassword`,
  `getNumberHashFromString`, `convertToBinary`, `binaryToString`
- [ ] **php-email** — `isEmail`, `emailString`, `usernameFromEmail`
- [ ] **php-html** *(existe)* — `DomHelper` (fusion), `HtmlHelper`, `htmlToText`,
  `convertUrlsToHyperLinks`, `toHtmlTable`, `WithDomId`
- [ ] **php-console** — `asciiColorWrap`, `toStringTable`, `ArrayToTextTable`
- [ ] **php-http** — content types de `HttpHelper`, partie pure de `RequestHelper`
  (`buildQueryString`…)
- [ ] **php-emoji** — `EmojiHelper`
- [ ] **php-fixture** — `LoremIpsumHelper`, placeholders `DEMO_URL` / `DEMO_EMAIL`
- [ ] **php-date** *(existe)* — `TimeHelper`, `DurationsHelper`
- [ ] **php-git** / **php-system** — `GitHelper` / `SystemHelper`
- [ ] **php-testing** — `WithArrayTestCase`

## Suppressions (0 usage dans la suite ni dans les apps scannées)

- [ ] `CodeHelper`, `StatusHelper`, `IconMaterialHelper`, `VariableSpecialHelper`
- [ ] une quinzaine de `Has*Trait` jamais utilisés (`HasCategory`, `HasDepth`,
  `HasFamily`, `HasOrigin`, `HasDateLastAccess`, `HasDisplayName`, `HasEmail`,
  `HasFeatured`, `HasFileName`, `HasJsonData`, `HasKey`, `HasMimeType`, `HasParent`,
  `HasPosition`, `HasTarget`, `HasVisibility`…) — revérifier au moment de supprimer

## Points d'attention

- Ce dont `symfony-bundle` dépend devient socle à son tour : le garder minimal ;
  php-string en est le candidat naturel, et il bouge peu.
- Le « 0 usage » ne vaut que pour les helpers statiques et les traits : services,
  extensions Twig, validators et voters sont branchés par autoconfig.
- `RequestHelper` dans symfony-routing oblige tunnels, user et testing à dépendre
  de routing.
- Apps à migrer en même temps : sapiens, SYRTIS (api : `NumberHelper::isIntegerLike`),
  MOJOE, RESPONSITE, EJM, ANDRE. `VariableHelper` y est le plus gros morceau.
- Vérifier l'absence de cycle à chaque déplacement (template ne dépend que de helpers,
  security et company n'ont aucune dépendance wexample au moment de l'analyse).
- Garder l'ancien namespace en alias le temps de la migration, ou migrer suite et apps
  d'un bloc : à trancher avant de commencer.
