# hgabka/seo-bundle

SEO és közösségi megosztási metaadatok kezelése oldalakhoz: meta title/description/robots, OpenGraph és Twitter Card adatok, egyedi fejléc-tartalom, szerkeszthető `robots.txt`, valamint Google Analytics segédek (inicializálás, e-commerce követés). A `hgabka/node-bundle` oldalszerkesztőjébe egy SEO fülként illeszkedik. A Kunstmaan SeoBundle alapján készült.

## Telepítés

```bash
composer require hgabka/seo-bundle
```

```php
// config/bundles.php
Hgabka\SeoBundle\HgabkaSeoBundle::class => ['all' => true],
```

A `robots.txt` kiszolgálásához a `RobotsController` attribútum-route-jait be kell tölteni:

```yaml
# config/routes.yaml
HgabkaSeoBundle_robots:
    resource: '@HgabkaSeoBundle/Controller/RobotsController.php'
    type: attribute
```

Követelmények: PHP ^8.2, Symfony 7/8. Függőségek: `hgabka/utils-bundle`, `hgabka/media-bundle` (OG/Twitter kép), Sonata Admin, LiipImagine (a bundle `prepend`-del hozzáadja a saját szűrőit: `Resources/config/imagine_filters.yaml`). Táblák: `hg_seo_seo`, `hg_seo_robots`.

## Konfiguráció

A bundle (`hgabka_seo`) saját konfigurációs fát nem definiál; a viselkedést paraméterek szabályozzák:

```yaml
parameters:
    google.analytics.account_id: 'UA-XXXXX-1'   # Google Analytics fiók (alapból üres)
    robots_default: "User-agent: *"             # a robots.txt alapértelmezett tartalma
    websitetitle: My site                       # kötelező: a weboldal címe (SeoManager)
```

A `SeoManager` a `websitetitle` konténer-paramétert (a weboldal címe) és a `kernel.project_dir`-t kapja – a `websitetitle` paramétert a projektben definiálni kell.

## Használat

### Entitás és admin

- `Entity\Seo` (`hg_seo_seo`) – oldalhoz (`ref_id` + `ref_entity_name`) kapcsolt rekord: `meta_title`, `meta_description`, `meta_author`, `meta_robots`, `og_*` (type, title, description, url, image, article author/publisher/section), `twitter_*` (title, description, site, creator, image), `extra_metadata` (szabad fejléc-HTML).
- `Entity\Robots` (`hg_seo_robots`) – a `robots.txt` szerkeszthető tartalma.
- Az `EventListener\NodeListener` a node-bundle oldalszerkesztő űrlapjára (`hgabka_node.adaptForm`) felteszi a SEO fület (`Form\SeoType`, `SocialType`); a `CloneListener` oldal-klónozáskor a SEO adatot is másolja.
- `Admin\SeoAdmin`, `Admin\RobotsAdmin` + menüpont (`Helper\Menu\SeoManagementMenuAdaptor`, `Admin\Menu\AdminMenuListener`); a robots.txt a `ROLE_SUPER_ADMIN` számára szerkeszthető (`HgabkaSeoBundle_settings_robots`).

### robots.txt

A `/robots.txt` útvonal sorrendben: az adatbázisban (`Robots`) tárolt tartalmat, majd a `robots.txt` fájlt, végül a `robots_default` paramétert adja vissza.

### Twig

A layout `<head>` részében:

```twig
{% if page is defined %}
    {{ render_seo_metadata_for(page) }}
{% endif %}
{{ render_general_seo_metadata() }}   {# az oldalfüggetlen alap SEO adatok #}
```

A cím meghatározása:

| Függvény | Sorrend |
|---|---|
| `get_title_for(page)` | SEO title → oldal címe → üres string |
| `get_title_for_page_or_default(page, 'Alap cím')` | SEO title → megadott alapérték → oldal címe |
| `get_title()` | az általános (weboldal) cím |

További függvények: `get_seo_for(page)` (a nyers `Seo` objektum), `get_absolute_url(url)`, `get_image_dimensions(src)`.

A kimenet a `Resources/views/SeoTwigExtension/metadata.html.twig` sablonnal módosítható (felülírható a projekt `templates/bundles/HgabkaSeoBundle/` mappájában).

### Google Analytics

A `google_analytics_initialize` és `google_analytics_track_order` Twig függvények a `analytics.js`-re (jQuery-függő; a bundle `Resources/public/js/analytics.js`) támaszkodnak.

```twig
{{ google_analytics_initialize() }}                                  {# a google.analytics.account_id paraméterrel #}
{{ google_analytics_initialize({'account_id': 'UA-XXXXX-1'}) }}      {# vagy explicit fiókkal #}
```

Debug módban a script nem tölt be, csak a konzolra írja a kéréseket. Automatikusan követi a letöltéseket, külső linkeket, email linkeket és a gombkattintásokat; a közösségi események követéséhez az SDK-k betöltése után hívd meg: `googleAnalyticsApi.trackSocial('optional pageurl', 'optional trackername')`.

E-commerce követés: állíts össze egy `Helper\Order` objektumot `Helper\OrderItem` tételekkel, add át a sablonnak, majd:

```php
$order = (new Order())->setShippingTotal(5)->setTransactionID('ORD2013-00231');
$order->orderItems[] = (new OrderItem())
    ->setName('ACME 3000 Starter Kit')->setQuantity(20)->setUnitPrice(13.5)
    ->setTaxes((20 * 13.5) * 0.21)->setCategoryorVariation('World Domination')->setSKU('ACM300-SK');
```

```twig
{{ google_analytics_track_order(order) }}
```
