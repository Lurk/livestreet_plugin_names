<?php

class PluginNames_ActionProfile extends ActionProfile
{
    protected function RegisterEvent()
    {
        $this->AddEvent('friendoffer', 'EventFriendOffer');
        $this->AddEvent('ajaxfriendadd', 'EventAjaxFriendAdd');
        $this->AddEvent('ajaxfrienddelete', 'EventAjaxFriendDelete');
        $this->AddEvent('ajaxfriendaccept', 'EventAjaxFriendAccept');

        $this->AddEventPreg('/'.Config::Get('plugin.phpbb.regular').'+$/ui', '/^(whois)?$/i', 'EventWhois');
        $this->AddEventPreg('/'.Config::Get('plugin.phpbb.regular').'+$/ui', '/^favourites$/i', '/^comments$/i', '/^(page(\d+))?$/i', 'EventFavouriteComments');
        $this->AddEventPreg('/'.Config::Get('plugin.phpbb.regular').'+$/ui', '/^favourites$/i', '/^(page(\d+))?$/i', 'EventFavourite');
    }
}

?>