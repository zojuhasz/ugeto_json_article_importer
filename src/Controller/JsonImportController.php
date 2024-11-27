<?php

namespace Drupal\json_article_importer\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

class JsonImportController extends ControllerBase {

  public function importArticles() {
    // A JSON fájl elérési útja.
    $json_file_path = DRUPAL_ROOT . '/sites/default/files/articles.json';
    if (!file_exists($json_file_path)) {
      return [
        '#type' => 'markup',
        '#markup' => $this->t('JSON file not found.'),
      ];
    }

    $json_data = file_get_contents($json_file_path);
    $articles = json_decode($json_data, TRUE);

    if ($articles === NULL) {
      return [
        '#type' => 'markup',
        '#markup' => $this->t('Invalid JSON data.'),
      ];
    }

    //print_r($articles);
    
return; //JZ    
    foreach ($articles as $article) {
      // Ellenőrizzük, hogy a szükséges adatok rendelkezésre állnak-e.
      //print_r($article['body'][0]);
      if (isset($article['title']) && isset($article['body']) ) {
        
         // Létrehozási idő beállítása
        $created_time = time(); // Alapértelmezett érték

        if (isset($article['created'])) {
            $created_array = $article['created'];
            if (is_array($created_array) && !empty($created_array)) {
                $created_item = reset($created_array); // Az első elem kiválasztása
                if (isset($created_item['value'])) {
                    $date_string = $created_item['value'];
                    // Konvertáljuk a dátumot UNIX timestampre
                    $created_time = strtotime($date_string);
                    if ($created_time === false) {
                        // Ha a konverzió sikertelen, használjuk az alapértelmezett értéket
                        $created_time = time();
                    }
                }
            }
        }

        
        
        
        $node = Node::create([
          'type' => 'article',
          'title' => $article['title'],
          'body' => [
            'value' => $article['body'][0]['value'],
            'format' => 'full_html',
          ],
           'created' => $created_time,
        ]);


        $node->save();
      }
    }

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Articles have been imported successfully.'),
    ];
  }

}


