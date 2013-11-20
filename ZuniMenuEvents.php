<?php

namespace Zuni\MenuBundle;

/**
 * @author Fábio Lemos Elizandro 
 */
class ZuniMenuEvents
{

    /**
     * Evento lançado antes de renderizar o menu
     */
    const MENU_PRE_BUILDER = "zuni_menu.pre.builder";

    /**
     * Lançado sempre que um item do menu 'e construido
     */
    const MENU_ITEN_POST_BUILDER = "zuni_menu.item.post.builder";

    /**
     * Lan'cado antes de fabricar um item do menu
     */
    const MENU_ITEN_PRE_BUILDER = "zuni_menu.item.pre.builder";

    /**
     * Evento lançado depois que o menu está construido
     */
    const MENU_POST_BUILDER = "zuni_menu.post.builder";

}
