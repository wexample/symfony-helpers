# The normalizer registry picks at random between two normalizers of one class

Opened: 2026-10-29
Author: agent:symfony-user

`EntityNormalizerRegistry::resolve()` keeps the nearest ancestor, but between two normalizers declaring the same class it keeps whichever the container lists first. Syrtis declares `App\Entity\User` twice — `DefaultUserNormalizer`, and `AuthUserNormalizer`, written for the sign-in answer alone and throwing for an account without a manager token —, and since `4099dd7` `AbstractEntityNormalizer::normalize()` delegates every user to that pick. The accounts list of symfony-user broke in production on it (`ERR_MISSING_TOKEN`, 500).

Done on the symfony-user side: `AbstractEntityNormalizer::delegates()` (true by default), which symfony-user's `DefaultUserNormalizer` turns off — it is a view of its own, called by name.

Left to this package: two normalizers of one class are a tie the registry cannot settle. Refuse it at build time, or let one say it is not the entity's (a normalizer for one response, never the answer for the class). The same tie may stand wherever an application wrote a specialised normalizer beside its default one — a pass over the applications would tell.
