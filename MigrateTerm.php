<?php
use Drupal\taxonomy\Entity\Term;

/*
 * @description : Migrate Themes
 * @pre_requested : fe_themes must be exists as Taxonomy with following field
 *  @field_theme_heading : Theme heading
 *  @field_theme_value : Theme value
 *  @field_theme_visible : Theme visibility
 */

function getParentID($level,$taxonomyTerm) {
  $query = \Drupal::entityQuery('taxonomy_term');
  $query->condition('vid', $taxonomyTerm);
  //$query->condition('name', $name);
  $query->condition('field_level', $level);
  $termResult = $query->execute();
  if ((!empty($termResult)) && (count($termResult)>0)) {
    return array_shift($termResult);
  } else {
    return 0;
  }
}

function getTermWeight($parentID) {
  $count = 0;
  $sql = "Select count(tid) as count from taxonomy_term_hierarchy where parent=$parentID";
  $result = db_query($sql);
  if ($result) {
    $data = $result->fetch();
    $count = ($data->count);    
  }
  return $count;
}

function getLevel($data,$level)
{
  $returnArray = array();
  $i = 0;
  while ($i<=$level)
  {
    if (!empty(trim($data[$i])))
    {
      $returnArray[] = trim($data[$i]);
    } else {
      break;
    }
    $i++;
  }
  return implode(':', $returnArray);
}
function createTerm($name,$parentID,$weight,$taxonomyTerm,$level) {
//  echo "\n";
//  echo "Name:$name,PID:$parentID,";
//  echo "\n";
//  print_r(implode(':',$treeIDs)); 
//  $parentID = ($treeIDs==null) ? 0 : end($treeIDs);
//  echo ",Parent ID:$parentID ";
//  echo "\n";
  
  $query = \Drupal::entityQuery('taxonomy_term');
  $query->condition('vid', $taxonomyTerm);
  //$query->condition('name', $name);
  $query->condition('field_level', $level);
  $termResult = $query->execute();
  if (count($termResult) <= 0) {
    $term = Term::create(array(
          'parent' => $parentID,
          'name' => $name,
          'description' => array(
            'value' => NULL,
            'format' => 'full_html')
          ,
          'weight' => $weight,
          'vid' => $taxonomyTerm,
          'field_level' => $level,
        ))->save();
    return true; 
    //End
    $weight++;
  } else {
    return false;
  }
}
$level        = 2;
$newRecord    = 0;
$weight       = 0;
$parentID     = 0;
$taxonomyTerm = 'tags';
$description  = NULL;
$row          = 0;
$level0Weight = 0;
$level1Weight = 0;
$level2Weight = 0;
$level3Weight = 0;
$level4Weight = 0;
$level5Weight = 0;
$level6Weight = 0;
$level7Weight = 0;
$fp           = fopen('csv/fm_taxonomy.csv', 'r');
//$fp           = fopen('/mnt/jeet/www/ip/csv/ip_taxonomy_2.csv', 'r');
if ($fp)
{
while (!feof($fp)) {

  $data = fgetcsv($fp);
  if ($row > 0) {
    //echo "<pre>"; print_r($data); echo "</pre>";die;
    $level0 = (!empty($data[0])) ? trim($data[0]) : null;
    $level1 = (!empty($data[1])) ? trim($data[1]) : null;
    $level2 = (!empty($data[2])) ? trim($data[2]) : null;
    $level3 = (!empty($data[3])) ? trim($data[3]) : null;
    $level4 = (!empty($data[4])) ? trim($data[4]) : null;
    $level5 = (!empty($data[5])) ? trim($data[5]) : null;
    $level6 = (!empty($data[6])) ? trim($data[6]) : null;
    $level7 = (!empty($data[7])) ? trim($data[7]) : null;
    $newRecord++;
    $treeIDs = [];
    if (!empty($level0)) {
      //Level 0  
      $level = getLevel($data,0);
      
      $return = createTerm($level0,0,$level0Weight,$taxonomyTerm,$level);
      if ($return==true)
      $level0Weight++;
      
      //Level 1
      if (!empty($level1))
      {
        $parentID   = getParentID($level,$taxonomyTerm);         
        $level      = getLevel($data,1);    
        $weight     = getTermWeight($parentID);
        $return     = createTerm($level1,$parentID,$weight,$taxonomyTerm,$level);
        
        
        //Level 2
        if (!empty($level2))
        {
          $parentID   = getParentID($level,$taxonomyTerm);
          $level      = getLevel($data,2);  
          $weight     = getTermWeight($parentID);     
          $return     = createTerm($level2,$parentID,$weight,$taxonomyTerm,$level);
          
          
            //Level 3
            if (!empty($level3))
            {
              $parentID   = getParentID($level,$taxonomyTerm);
              $level      = getLevel($data,3);
              $weight     = getTermWeight($parentID);     
              $return     = createTerm($level3,$parentID,$weight,$taxonomyTerm,$level);
              
              
                //Level 4
                if (!empty($level4))
                {
                  $parentID   = getParentID($level,$taxonomyTerm);
                  $level      = getLevel($data,4);
                  $weight     = getTermWeight($parentID);    
                  $return     = createTerm($level4,$parentID,$weight,$taxonomyTerm,$level);
                    //Level 5
                    if (!empty($level5))
                    {
                      $parentID   = getParentID($level,$taxonomyTerm);
                      $level      = getLevel($data,5);
                      $weight     = getTermWeight($parentID);   
                      $return     = createTerm($level5,$parentID,$weight,$taxonomyTerm,$level);
                      
                        //Level 6
                        if (!empty($level6))
                        {
                          $parentID   = getParentID($level,$taxonomyTerm);
                          $level      = getLevel($data,6);
                          $weight     = getTermWeight($parentID);      
                          $return     = createTerm($level6,$parentID,$weight,$taxonomyTerm,$level);
                          
                            //Level 7
                            if (!empty($level7))
                            {
                              $parentID   = getParentID($level,$taxonomyTerm);
                              $level      = getLevel($data,7);
                              $weight     = getTermWeight($parentID);     
                              $return     = createTerm($level7,$parentID,$weight,$taxonomyTerm,$level);
                              
                            }
                        
                        }
                    
                    }
                }

            }
        }
      
      }      
    }
    echo "\n $row : $level";
  //echo "\n----------------------------------------------------------- \n";    
  }
  $row++;
}
} else {
  echo "\n CSV File does not exists";
}
echo "\n Total Created : $newRecord";
