<?php

$APP_CONF['NEWS_TITLE_OPTIONAL'] = false; //Сделать заголовок необязательным
$APP_CONF['NEWS_DESCRIPTION_OPTIONAL'] = false; // Сделать краткое описание необязательным
$APP_CONF['NEWS_ENABLE_SUBSCRIPTION'] = false;  //Включить подписку на новости
$APP_CONF['NEWS_SEND_EMAIL_STATUS'] = true; //???
$APP_CONF['NEWS_DISABLE_LINK'] = true;  //Выключит вставку ссылки в описание
$APP_CONF['NEWS_ENABLE_CATEGORIES'] = true; //Включить категории
$APP_CONF['NEWS_RSS_ENABLED'] = false; //Включить RSS для новостей
$APP_CONF['NEWS_RSS_URL'] = '/rss'; //Путь к RSS
$APP_CONF['NEWS_RSS_TITLE'] = 'Наши новости'; //Заголовок RSS фида
$APP_CONF['NEWS_RSS_DESCRIPTION'] = 'Последние новости'; //Описание RSS фида
$APP_CONF['NEWS_POST_URL'] = '/news/post/:category_id/:id'; // Шаблон ссылки к посту
$APP_CONF['NEWS_ENABLE_MORE'] = false; //Включить кат
$APP_CONF['NEWS_DESCRIPTION_MAX_LENGTH'] = -1; //Максимальная длина описания, -1 = неограниченно
$APP_CONF['NEWS_DESCRIPTION_MIN_LENGTH'] = -1; //Минимальная длина описания, -1 = неограничено
$APP_CONF['NEWS_EXTRA_IMAGE'] = false; //
$APP_CONF['NEWS_TRANSLIT_URLS'] = false;
$APP_CONF['NEWS_PHOTOS_HTML_DESCRIPTIONS'] = false;
$APP_CONF['RSS_ONLY_CONTENT'] = false;
$APP_CONF['NEWS_UNSUBSCRIBE_URL'] = '/news/unsubscribe'; // Адрес страницы отписки
$APP_CONF['DISABLED_FORM_FIELDS']['News_Post'] = array('source_url', 'source_title');
$APP_CONF['NEWS_ALLOW_MULTIPLE_IMPORTANT_POSTS'] = false; // Разрешить множественные важные новости.
$APP_CONF['NEWS_POST_SITEMAP_URL'] = '/news/post/%s';

Phpr::$router->addPrefix('news-prefix', 'news');
Phpr::$router->addRule('$news-prefix/post/:id', 'news_post')->controller('news')->action('post')->check('id', "/^\d+$/");

// Закомментировать если нет категорий
Phpr::$router->addRule('$news-prefix/category/:id/:page', 'news_category')->controller('news')->action('category')->check('id', "/^\d+$/")->check('page', "/^\d+$/")->def('page', NULL);
Phpr::$router->addRule('$news-prefix/:page', 'news_index')->controller('news')->action('index')->check('page', "/^\d+$/")->def('page', NULL);

