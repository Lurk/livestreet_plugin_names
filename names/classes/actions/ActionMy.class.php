<?php

class PluginNames_ActionMy extends ActionMy
{

    protected function RegisterEvent()
    {
        $this->AddEventPreg('/'.Config::Get('plugin.names.regular').'+$/ui', '/^(page(\d+))?$/i', 'EventTopics');
        $this->AddEventPreg('/'.Config::Get('plugin.names.regular').'+$/ui', '/^blog$/i', '/^(page(\d+))?$/i', 'EventTopics');
        $this->AddEventPreg('/'.Config::Get('plugin.names.regular').'+$/ui', '/^comment$/i', '/^(page(\d+))?$/i', 'EventComments');
    }

}

?>