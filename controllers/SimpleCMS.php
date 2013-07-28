<?php

class SimpleCMS {
    private $router;
    private $loader;
    private $twig;

    public function SimpleCMS($url) {
        // load classes
        require_once 'Router.php';
        require_once 'controllers/libs/Twig/Autoloader.php';
        
        // init the classes
        Twig_Autoloader::register();
        $this->router = new Router();
        $this->loader = new Twig_Loader_Filesystem('views');
        $this->twig = new Twig_Environment($this->loader);
        $this->root = $this->router->getRoot();
        $this->sections = $this->router->getSections();
        $this->sectionstring = $this->router->getSectionsString();
        
        // load and parse the data
        $urls = $this->loadFile('models/'.$url.'.json');
        $page = $this->checkUrls($urls);
        $items = $this->loadModel($page);
        $html = $this->loadView($items);
        
        //print_r($items);
        echo $html;
    }
    
    /**
     * Check which url matches the data closest
     * @param {Object} items A list of data items to loop through
     */
    private function checkUrls($items) {
        foreach ($items as $item) {
            if (isset($item['url'])) {
                //echo $item['url'].' '.$this->sectionstring.' '.fnmatch($item['url'], $this->sectionstring)."\n";
                if (fnmatch($item['url'], $this->sectionstring)) {
                    return $item;
                }
            }
        }
        return $items[0];
    }
    
    /**
     * Load a json file and convert to a php object
     * @param {String} url The url of the file to load
     */
    private function loadFile($url) {
        $string = file_get_contents($url);
        return json_decode($string, true);
    }
    
    /**
     * Loop through the data and load models
     * @param {Object} items A list of data items to loop through
     */
    private function loadModelList($items) {
        foreach ($items as &$item) {
            $item = $this->loadModel($item);
        }
        return $items;
    }
    
    /**
     * Load a single model
     * @param {Object} item single object item
     */
    private function loadModel($item) {
        if (isset($item['model'])) {
            if (!is_array($item['model'])) {
                $item['model'] = $this->loadFile('models/'.$item['model'].'.json');
            }
            $item['model'] = $this->loadModelList($item['model']);
            if (isset($item['index'])) {
                $item['item'] = $item['model'][$item['index']];
            }
        }
        return $item;
    }
    
    /**
     * Loop through the data and load views
     * @param {Object} items A list of data items to loop through
     */
    private function loadViewList($items) {
        $html = '';
        foreach ($items as &$item) {
            $html .= $this->loadView($item);
        }
        return $html;
    }
    
    /**
     * Load a single view
     * @param {Object} item single object item
     */
    private function loadView($item) {
        if (isset($item['model'])) {
            $item['html'] = $this->loadViewList($item['model']);
        }
        if (isset($item['view'])) {
            if (isset($item['classes'])) {
                $item['classes'] = strtolower($item['view']).' '.$item['classes'];
            } else {
                $item['classes'] = strtolower($item['view']);
            }
            $item['root'] = $this->root;
            $item['sections'] = $this->sections;
            if (file_exists('views/'.$item['view'].'.html')) {
                return $this->twig->render($item['view'].'.html', $item);
            } else {
                return $this->twig->render('Default.html', $item);
            }
        }
        return '';
    }
}
?>