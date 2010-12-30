<?php

class PluginNames extends Plugin {


    protected $aInherits = array(
        'action' => array('ActionMy', 'ActionProfile','ActionLogin'),

    );

    public function Activate() {
        return true;
    }

    public function Deactivate() {
        return true;
	}


    public function Init() {
    }
}

?>