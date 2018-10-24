<?php
class SmartblogOverride extends Smartblog
{
    
    public function hookModuleRoutes($params)
    {
        $alias = Configuration::get('smartmainblogurl');
        $usehtml = (int)Configuration::get('smartusehtml');
        if ($usehtml != 0) {
            $html = '.html';
        } else {
            $html = '';
        }
        $my_link = array(
            'smartblog' => array(
                'controller' => 'category',
                'rule' => $alias . $html,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_list' => array(
                'controller' => 'category',
                'rule' => $alias . '/category' . $html,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_list_module' => array(
                'controller' => 'category',
                'rule' => 'module/' . $alias . '/category' . $html,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_list_pagination' => array(
                'controller' => 'category',
                'rule' => $alias . '/category/page/{page}' . $html,
                'keywords' => array(
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_pagination' => array(
                'controller' => 'category',
                'rule' => $alias . '/page/{page}' . $html,
                'keywords' => array(
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_category' => array(
                'controller' => 'category',
                'rule' => $alias . '/category/{id_category}-{slug}.html',
                'keywords' => array(
                    'id_category' => array('regexp' => '[0-9]*', 'param' => 'id_category'),
                    'slug' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_category_pagination' => array(
                'controller' => 'category',
                'rule' => $alias . '/category/{id_category}_{slug}/page/{page}' . $html,
                'keywords' => array(
                    'id_category' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'id_category'),
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                    'slug' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_cat_page_mod' => array(
                'controller' => 'category',
                'rule' => 'module/' . $alias . '/category/{id_category}_{slug}/page/{page}' . $html,
                'keywords' => array(
                    'id_category' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'id_category'),
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                    'slug' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_post' => array(
                'controller' => 'details',
                'rule' => $alias . '/entry/{id_post}-{slug}.html',
                'keywords' => array(
                    'id_post' => array('regexp' => '[0-9]*', 'param' => 'id_post'),
                    'slug' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_search' => array(
                'controller' => 'search',
                'rule' => $alias . '/search' . $html,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_tag' => array(
                'controller' => 'tagpost',
                'rule' => $alias . '/tag/{tag}' . $html,
                'keywords' => array(
                    'tag' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'tag'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_search_pagination' => array(
                'controller' => 'search',
                'rule' => $alias . '/search/page/{page}' . $html,
                'keywords' => array(
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_archive' => array(
                'controller' => 'archive',
                'rule' => $alias . '/archive' . $html,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_archive_pagination' => array(
                'controller' => 'archive',
                'rule' => $alias . '/archive/page/{page}' . $html,
                'keywords' => array(
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_month' => array(
                'controller' => 'archive',
                'rule' => $alias . '/archive/{year}/{month}' . $html,
                'keywords' => array(
                    'year' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
                    'month' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'month'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_month_pagination' => array(
                'controller' => 'archive',
                'rule' => $alias . '/archive/{year}/{month}/page/{page}' . $html,
                'keywords' => array(
                    'year' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
                    'month' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'month'),
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_year' => array(
                'controller' => 'archive',
                'rule' => $alias . '/archive/{year}' . $html,
                'keywords' => array(
                    'year' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
            'smartblog_year_pagination' => array(
                'controller' => 'archive',
                'rule' => $alias . '/archive/{year}/page/{page}' . $html,
                'keywords' => array(
                    'year' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'year'),
                    'page' => array('regexp' => '[_a-zA-Z0-9-\pL]*', 'param' => 'page'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => 'smartblog',
                ),
            ),
        );
        return $my_link;
    }
    
}
?>