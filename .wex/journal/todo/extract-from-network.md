# Helpers: leftovers from network (App imports, repository shortcuts, entity routes, small methods)

Opened: 2026-09-24
Updated: 2026-09-24
Author: agent:archeology

## Read this first — status of this todo

> **This is a proposal for discussion, not an order to code.** It was written by the 2026-09 network archaeology pass. Read it, then discuss it with the owner: every design choice and recommendation below is to be challenged and validated **before** any code is written. Do not start implementing on your own.
>
> - Context: `/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/local/network/.wex/knowledge/readme/archeology/index.md.j2` (entry point, order between packages), then `sources.md.j2` (where the legacy code lives: archive repo, branch checkouts, GitLab issues) and the domain page linked below.
> - Pending owner decisions affecting this work are listed in `/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/local/network/.wex/knowledge/readme/archeology/recap.md.j2`, section "Décisions qui t'attendent". Where this todo assumes an answer, treat it as an open question.
> - Safety: `NETWORK/local/network` runs on **production data** (real bookkeeping, real invoices in `var/`, a prod dump in `.wex/mysql/dumps/`) — read its code only, never run anything against it. Anonymize any fixture taken from network (bank exports, FEC, mails contain real names/accounts). Never copy secrets found in its history (Stripe keys, tokens, passwords, private keys).

## Goal

symfony-helpers holds almost all of network's helpers. This todo closes the remaining gaps and removes leftovers that still import network classes. Details: `/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/local/network/.wex/knowledge/readme/archeology/already-extracted-check.md.j2`, sections "Broken or unfinished extractions", "Helpers" and "API, entity routing…".

## Steps

1. **Remove the `App\` imports.** Files: `src/Repository/AbstractUserRepository.php` and `src/Entity/Traits/LinkedToUserTrait.php` (`App\Entity\User`: use the symfony-user interface or a resolve_target_entities mapping); `src/Service/Entity/Traits/WithMessageEntityServiceTrait.php` (use symfony-translations). **Delete** `src/Service/AbstractEntitySearchService.php`, a stale copy superseded by symfony-search. Check with `grep -rn "use App" src` → empty.
2. **Obsolete helpers.** Delete `src/Helper/IconMaterialHelper.php` (symfony-template icons replace it) after updating `symfony-accounting/src/Form/Traits/FrBankInfo2018Trait.php`. Merge or delete `FormHelper::buildRoute` (it duplicates the symfony-forms FormHelper). Move `PriceHelper` to symfony-money if the money todo has not done it yet.
3. **Repository shortcuts.** Source: `/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/archeo/trees/develop-131-fos-user/src/Wex/BaseBundle/Repository/AbstractRepository.php`: `querySearchLike/Equal/Number/NumberAbsolute/EqualNonZeroIntData/StringOrArray`, `queryIsInArray`, `queryIsOfType(s)`, `queryJoin/JoinManyToOne` (#234), `queryEntityRelation`, `querySelectEntity`, `queryOrderBy(Array)`, `queryByFieldNull`, `queryForEntities(Ids)`, `findCount`, `findAllSortedBy`, `findIsUniqueOrNotExistingBy`, `removeAllAndFlush`. Check each against the existing `AbstractRepository` and `SearchableRepositoryTrait` before adding it. Tests on a fixture entity.
4. **Repository traits.** Source: `/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/archeo/trees/develop-131-fos-user/src/Wex/BaseBundle/Repository/Traits/{HasStatus,HasFilterTag,WithDateCreated,LinkedToAnyEntity}RepositoryTrait.php`, paired with the `HasStatusTrait`, `HasDateCreatedTrait` and `LinkedToEntityTrait` entity traits. HasFilterTag is shared with the symfony-api todo; do it here and let symfony-api consume it.
5. **Small methods.** `EntityHelper::isEntity`; `HasStatusTrait::hasStatusDisabled/getStatusesDisabled`; `HasTitleTrait::getDisplayTitle`; `HasUrlTrait`, `HasAmountTrait` (from `/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/archeo/trees/develop-131-fos-user/src/Wex/BaseBundle/Entity/Traits/HasUrlTrait.php`, `WithAmountTrait.php`); `EntityNeutralService::cloneField(s)/editField/getUrl` (`/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/archeo/trees/develop-131-fos-user/src/Wex/BaseBundle/Service/EntityNeutralService.php`); `AbstractController::fileDownload/redirectToControllerRoute/getGrantedLevel` (`/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/archeo/trees/develop-131-fos-user/src/Wex/BaseBundle/Controller/AbstractController.php`); Twig `entity_url(entity, action)` (`/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/archeo/trees/develop-131-fos-user/src/Wex/BaseBundle/Twig/EntityExtension.php`).
6. **Entity route loader.** Source: `/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/archeo/trees/develop/src/Wex/BaseBundle/Routing/AbstractEntityRouteLoaderService.php` (develop version, already on package namespaces), plus `/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/archeo/trees/develop-131-fos-user/src/Routing/EntityRouteLoader.php`. Write an `EntityRoutesRouteLoader` next to `SimpleRoutesRouteLoader`. Controllers are discovered by an attribute. Actions are declared as data (list/show/edit, role, optional API twin). Paths are `<role-kebab>/<entity-kebab>/<action>` and route names `<entity>_<action>`. Add a template fallback `pages/entity/admin/<view>`. Drop the old `vue` options. Kernel test.
7. **Date helper.** `DateHelper::generateFromYear` (`/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/archeo/trees/develop-131-fos-user/src/Wex/BaseBundle/Helper/DateHelper.php:49`) goes to php-date. `RequestHelper::urlAddQueryStrings/parseQueryString/buildUrl` (`/home/weeger/Desktop/WIP/WEB/WEXAMPLE/NETWORK/archeo/trees/develop-131-fos-user/src/Wex/BaseBundle/Helper/RequestHelper.php:54`) goes to php-helpers, with a Twig `url_add_query_strings` in symfony-routing. Report both to those packages.

## Do not

- Do not port `BundleHelper::WEX_*`, `VariableHelper::_EMPTY_`, `LoggingService`, `AdaptiveResponse`/`AdaptiveEventsBag`, `VariableEntityTypeControllerTrait` or `AdminEntityControllerTrait`.
