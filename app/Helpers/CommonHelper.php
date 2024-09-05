<?php

namespace App\Helpers;

final class CommonHelper {
    public function parseTree($tree, $root = 0) {
        $return = array();
        # Traverse the tree and search for direct children of the root
        foreach($tree as $child => $parent) { 
            # A direct child is found
            //echo $parent->id; die();
            if($parent->parent == $root) {
                # Remove item from tree (we don't need to traverse this again)
                unset($tree[$child]);
                # Append the child into result array and parse its children
                $return[] = array(
                    'id' => $parent->id,
                    'text' => $parent->name,
                    'children' => $this->parseTree($tree, $parent->id)
                );
            }
        }
        return empty($return) ? null : $return;    
    }
}
?>