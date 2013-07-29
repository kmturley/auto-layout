<?php

class SimpleCMS {
    private $router;
    private $loader;
    private $twig;

    public function SimpleCMS($url) {
        require_once 'Router.php';
        require_once 'controllers/libs/Twig/Autoloader.php';
        
        // init the classes
        Twig_Autoloader::register();
        $this->loader = new Twig_Loader_Filesystem('views');
        $this->twig = new Twig_Environment($this->loader);
        $this->router = new Router();
        
        // set some globals
        $this->root = $this->router->getRoot();
        $this->sections = $this->router->getSections();
        
        // load the correct page data and any dependancies
        $urls = $this->loadFile('models/'.$url.'.json');
        $page = $this->checkUrls($urls, $this->router->getSectionsString());
        $items = $this->loadModel($page);
        $html = $this->twig->render('Base.html', array('item' => $items));
        
        //print_r($items);
        echo $html;
    }
    
    /**
     * Check which url matches the data closest
     * @param {Object} items A list of data items to loop through
     */
    private function checkUrls($items, $url) {
        foreach ($items as $item) {
            //echo $item['url'].' '.$this->sectionstring.' '.fnmatch($item['url'], $this->sectionstring)."<br/>\n";
            if (fnmatch($item['view'], $url)) {
                return $item;
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
            $item['root'] = $this->root;
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
        }
        $item['root'] = $this->root;
        $item['sections'] = $this->sections;
        return $item;
    }
}
?>