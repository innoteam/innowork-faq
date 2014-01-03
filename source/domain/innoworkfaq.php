<?php
// ----- Initialization -----
//
require_once('innowork/faq/InnoworkFaqCategory.php');
require_once('innowork/faq/InnoworkFaqNode.php');
require_once('innomatic/wui/Wui.php');
require_once('innomatic/wui/widgets/WuiWidget.php');
require_once('innomatic/wui/widgets/WuiContainerWidget.php');
require_once('innomatic/wui/dispatch/WuiEventsCall.php');
require_once('innomatic/wui/dispatch/WuiEvent.php');
require_once('innomatic/wui/dispatch/WuiEventRawData.php');
require_once('innomatic/wui/dispatch/WuiDispatcher.php');
require_once('innomatic/locale/LocaleCatalog.php');
require_once('innomatic/locale/LocaleCountry.php');

    global $gLocale, $gPage_title, $gXml_def, $gPage_status, $gInnowork_core;

require_once('innowork/core/InnoworkCore.php');
$gInnowork_core = InnoworkCore::instance('innoworkcore',
    \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
    \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess()
    );

$gLocale = new LocaleCatalog(
    'innowork-faq::innoworkfaq_domain_main',
    \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getLanguage()
    );

$gWui = Wui::instance('wui');
$gWui->loadWidget( 'xml' );
$gWui->loadWidget( 'innomaticpage' );
$gWui->loadWidget( 'innomatictoolbar' );

$gXml_def = $gPage_status = '';
$gPage_title = $gLocale->getStr( 'faq.title' );
$gCore_toolbars = $gInnowork_core->getMainToolBar();
$gToolbars['faq'] = array(
    'faqlist' => array(
        'label' => $gLocale->getStr( 'faqlist.toolbar' ),
        'themeimage' => 'listdetailed',
        'horiz' => 'true',
        'action' => WuiEventsCall::buildEventsCallString( '', array( array(
            'view',
            'default',
            '' ) ) )
        )
    );
/*
    'newfaq' => array(
        'label' => $gLocale->getStr( 'newfaq.toolbar' ),
        'themeimage' => 'filenew',
        'horiz' => 'true',
        'action' => WuiEventsCall::buildEventsCallString( '', array( array(
            'view',
            'newfaq',
            '' ) ) )
        ),
    'newfaqnode' => array(
        'label' => $gLocale->getStr( 'newfaqnode.toolbar' ),
        'themeimage' => 'filenew',
        'horiz' => 'true',
        'action' => WuiEventsCall::buildEventsCallString( '', array( array(
            'view',
            'newfaqnode',
            '' ) ) )
        )
    );
*/
// ----- Action dispatcher -----
//
$gAction_disp = new WuiDispatcher( 'action' );

$gAction_disp->addEvent(
    'newcat',
    'action_newcat'
    );
function action_newcat($eventData)
{
    global $gLocale, $gPage_status;

    $innowork_cat = new InnoworkFaqCategory(
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess()
        );

    $innowork_cat->Create(
        $eventData,
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId()
        );

    if ( $eventData['parentid'] != 0 ) {
        $innowork_cat->mAcl->CopyAcl(
            'faqcategory',
            $eventData['parentid']
            );
    }

    $gPage_status = $gLocale->getStr( 'cat_added.status' );
}

$gAction_disp->addEvent(
    'editcat',
    'action_editcat'
    );
function action_editcat($eventData)
{
    global $gLocale, $gPage_status;

    $innowork_cat = new InnoworkFaqCategory(
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess(),
        $eventData['id']
        );

    $innowork_cat->Edit(
        $eventData,
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId()
        );

    $gPage_status = $gLocale->getStr( 'cat_updated.status' );
}

$gAction_disp->addEvent(
    'removecat',
    'action_removecat'
    );
function action_removecat($eventData)
{
    global $gLocale, $gPage_status;

    $innowork_cat = new InnoworkFaqCategory(
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess(),
        $eventData['id']
        );

    $innowork_cat->Trash();

    $gPage_status = $gLocale->getStr( 'cat_removed.status' );
}

$gAction_disp->addEvent(
    'newfaq',
    'action_newfaq'
    );
function action_newfaq($eventData)
{
    global $gLocale, $gPage_status;

    $innowork_faq = new InnoworkFaqNode(
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess()
        );

    $innowork_faq->Create(
        $eventData,
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId()
        );

    if ( $eventData['parentid'] != 0 ) {
        $innowork_faq->mAcl->CopyAcl(
            'faqcategory',
            $eventData['parentid']
            );
    }

    $gPage_status = $gLocale->getStr( 'faq_added.status' );
}

$gAction_disp->addEvent(
    'editfaq',
    'action_editfaq'
    );
function action_editfaq($eventData)
{
    global $gLocale, $gPage_status;

    $innowork_faq = new InnoworkFaqNode(
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess(),
        $eventData['id']
        );

    $innowork_faq->Edit(
        $eventData,
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId()
        );

    $gPage_status = $gLocale->getStr( 'faq_updated.status' );
}

$gAction_disp->addEvent(
    'removefaq',
    'action_removefaq'
    );
function action_removefaq($eventData)
{
    global $gLocale, $gPage_status;

    $innowork_faq = new InnoworkFaqNode(
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess(),
        $eventData['id']
        );

    $innowork_faq->Trash();

    $gPage_status = $gLocale->getStr( 'faq_removed.status' );
}

$gAction_disp->addEvent(
    'cut',
    'action_cut'
    );
function action_cut(
    $eventData
    )
{
    global $gLocale, $gPage_status;

    switch ( $eventData['type'] ) {
    case 'category':
        $innowork_cat = new InnoworkFaqCategory(
            \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
            \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess(),
            $eventData['id']
            );
        $innowork_cat->Cut();
        break;

    case 'node':
        $innowork_faq = new InnoworkFaqNode(
            \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
            \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess(),
            $eventData['id']
            );
        $innowork_faq->Cut();
        break;
    }

    $gPage_status = $gLocale->getStr( 'cut_ok.status' );
}

$gAction_disp->addEvent(
    'paste',
    'action_paste'
    );
function action_paste(
    $eventData
    )
{
    global $gLocale, $gPage_status;

    $innowork_cat = new InnoworkFaqCategory(
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess(),
        $eventData['id']
        );
    $innowork_cat->Paste();

    $gPage_status = $gLocale->getStr( 'paste_ok.status' );
}

$gAction_disp->Dispatch();

// ----- Main dispatcher -----
//
$gMain_disp = new WuiDispatcher( 'view' );

function build_faq_tree($catList, $nodesList, $level = 1, $id = 0)
{
    $array = array();

    foreach ( $catList[$id] as $data ) {
        $array['cats'][$data['id']]['data'] = $data;
        $array['cats'][$data['id']]['child'] = build_faq_tree( $catList, $nodesList, $level + 1, $data['id'] );

        foreach ( $nodesList[$data['id']] as $node_data ) {
            $array['nodes'][$data['id']] = $node_data;
        }
    }

    return $array;
}

function build_faq_menu_tree($catList, $nodesList, $level = 1, $id = 0)
{
    global $gLocale;

    $dots = '';

    for ( $i = 1; $i <= $level; $i++ ) $dots .= '.';

    foreach ( $catList[$id] as $data ) {
        $menu .= $dots.'|'.( strlen( $data['title'] ) > 25 ? substr( $data['title'], 0, 23 ).'...' : $data['title'] ).'|'.WuiEventsCall::buildEventsCallString(
            '',
            array(
                array(
                    'view',
                    'showcat',
                    array(
                        'id' => $data['id'],
                        'iframe' => '1'
                        )
                    )
                )
            ).'|'.$data['title'].'||faqop'."\n";

        $menu .= build_faq_menu_tree( $catList, $nodesList, $level + 1, $data['id'] );

        $menu .= $dots.'.|'.$gLocale->getStr( 'newitem.label' ).'|'.WuiEventsCall::buildEventsCallString(
            '',
            array(
                array(
                    'view',
                    'newitem',
                    array(
                        'parentid' => $data['id'],
                        'iframe' => '1'
                        )
                    )
                )
            ).'|||faqop'."\n";

        foreach ( $nodesList[$data['id']] as $node_data ) {
            $menu .= $dots.'.|'.( strlen( $node_data['title'] ) > 25 ? substr( $node_data['title'], 0, 23 ).'...' : $node_data['title'] ).'|'.WuiEventsCall::buildEventsCallString(
            '',
            array(
                array(
                    'view',
                    'showfaq',
                    array(
                        'id' => $node_data['id'],
                        'iframe' => '1'
                        )
                    )
                )
            ).'|'.$node_data['title'].'||faqop'."\n";
        }
    }

    if ( $level == 1 ) {
        $menu .= '.|'.$gLocale->getStr( 'newitem.label' ).'|'.WuiEventsCall::buildEventsCallString(
            '',
            array(
                array(
                    'view',
                    'newitem',
                    array(
                        'parentid' => '0',
                        'iframe' => '1'
                        )
                    )
                )
            ).'|||faqop'."\n";

        foreach ( $nodesList[0] as $node_data ) {
            $menu .= '.|'.( strlen( $node_data['title'] ) > 25 ? substr( $node_data['title'], 0, 23 ).'...' : $node_data['title'] ).'|'.WuiEventsCall::buildEventsCallString(
            '',
            array(
                array(
                    'view',
                    'showfaq',
                    array(
                        'id' => $node_data['id'],
                        'iframe' => '1'
                        )
                    )
                )
            ).'|'.$node_data['title'].'||faqop'."\n";
        }
    }

    return $menu;
}

$gMain_disp->addEvent(
    'default',
    'main_default'
    );
function main_default($eventData)
{
    global $gLocale, $gPage_title, $gXml_def, $gPage_status, $gInnowork_core;

    $faq_cat = new InnoworkFaqCategory(
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess()
        );
    $cat_search = $faq_cat->Search(
        '',
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId()
        );

    $faq_node = new InnoworkFaqNode(
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess()
        );
    $nodes_search = $faq_node->Search(
        '',
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId()
        );

    $cat_list = array();
    foreach ( $cat_search as $id => $data ) {
        $cat_list[$data['parentid']][] = array(
            'title' => $data['title'],
            'id' => $data['id']
            );
    }

    $nodes_list = array();
    foreach ( $nodes_search as $id => $data ) {
        $nodes_list[$data['parentid']][] = array(
            'title' => $data['question'],
            'id' => $data['id']
            );
    }

    $menu = build_faq_menu_tree( $cat_list, $nodes_list );

    $gXml_def =
'<horizgroup>
  <args>
    <align>top</align>
  </args>
  <children>

    <vertframe>
      <children>

        <link>
          <args>
            <link type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'showcat',
                    array(
                        'id' => '0',
                        'iframe' => '1'
                        )
                    )
            ) ) ).'</link>
            <label type="encoded">'.urlencode( $gLocale->getStr( 'faqlist.label' ) ).'</label>
            <bold>true</bold>
            <target>faqop</target>
          </args>
        </link>';

    if ( strlen( $menu ) ) $gXml_def .=
'    <treevmenu>
      <args>
        <menu type="encoded">'.urlencode( $menu ).'</menu>
      </args>
    </treevmenu>';

    $gXml_def .=
'<horizbar/>

        <button>
          <args>
            <themeimage>reload</themeimage>
            <horiz>true</horiz>
            <frame>false</frame>
            <label type="encoded">'.urlencode( $gLocale->getStr( 'reload.button' ) ).'</label>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'default'
                    )
            ) ) ).'</action>
          </args>
        </button>

      </children>
    </vertframe>

    <vertframe>
      <children>

    <iframe><name>faqop</name>
      <args>
        <height>550</height>
        <width>600</width>
            <source type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'showcat',
                    array(
                        'id' => '0',
                        'iframe' => '1'
                        )
                    )
            ) ) ).'</source>
      </args>
    </iframe>

      </children>
    </vertframe>

  </children>
</horizgroup>';

}

$gMain_disp->addEvent(
    'newitem',
    'main_newitem'
    );
function main_newitem(
    $eventData
    )
{
    global $gXml_def, $gLocale, $gPage_title;

    $gXml_def =
'<vertgroup>
  <children>

    <grid>
      <children>

        <button row="0" col="0">
          <args>
            <themeimage>filenew</themeimage>
            <horiz>true</horiz>
            <frame>false</frame>
            <label type="encoded">'.urlencode( $gLocale->getStr( 'newcategory.button' ) ).'</label>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'newcategory',
                    array(
                        'parentid' => $eventData['parentid'],
                        'iframe' => '1'
                        )
                    )
            ) ) ).'</action>
          </args>
        </button>

        <button row="1" col="0">
          <args>
            <themeimage>filenew</themeimage>
            <horiz>true</horiz>
            <frame>false</frame>
            <label type="encoded">'.urlencode( $gLocale->getStr( 'newfaq.button' ) ).'</label>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'newfaq',
                    array(
                        'parentid' => $eventData['parentid'],
                        'iframe' => '1'
                        )
                    )
            ) ) ).'</action>
          </args>
        </button>

      </children>
    </grid>

  </children>
</vertgroup>';
}

$gMain_disp->addEvent(
    'newcategory',
    'main_newcategory'
    );
function main_newcategory(
    $eventData
    )
{
    global $gXml_def, $gLocale, $gPage_title;

    $gXml_def =
'<vertgroup>
  <children>

    <table>
      <args>
        <headers type="array">'.WuiXml::encode(
            array( '0' => array(
                'label' => $gLocale->getStr( 'newcategory.label' )
                ) ) ).'</headers>
      </args>
      <children>

    <form row="0" col="0"><name>category</name>
      <args>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'showcat',
                    array(
                        'id' => $eventData['parentid'],
                        'iframe' => $eventData['iframe']
                        )
                    ),
                array(
                    'action',
                    'newcat',
                    array(
                        'parentid' => $eventData['parentid']
                        ) )
            ) ) ).'</action>
      </args>
      <children>

        <grid>
          <children>

            <label row="0" col="0">
              <args>
                <label type="encoded">'.urlencode( $gLocale->getStr( 'category.label' ) ).'</label>
              </args>
            </label>
            <string row="1" col="0"><name>title</name>
              <args>
                <disp>action</disp>
                <size>25</size>
              </args>
            </string>

            <label row="2" col="0">
              <args>
                <label type="encoded">'.urlencode( $gLocale->getStr( 'description.label' ) ).'</label>
              </args>
            </label>
            <text row="3" col="0"><name>description</name>
              <args>
                <disp>action</disp>
                <rows>5</rows>
                <cols>50</cols>
              </args>
            </text>

          </children>
        </grid>

      </children>
    </form>

        <button row="1" col="0"><name>apply</name>
          <args>
            <themeimage>buttonok</themeimage>
            <horiz>true</horiz>
            <frame>false</frame>
            <formsubmit>category</formsubmit>
            <label type="encoded">'.urlencode( $gLocale->getStr( 'apply.button' ) ).'</label>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'showcat',
                    array(
                        'id' => $eventData['parentid'],
                        'iframe' => $eventData['iframe']
                        )
                    ),
                array(
                    'action',
                    'newcat',
                    array(
                        'parentid' => $eventData['parentid']
                        ) )
            ) ) ).'</action>
          </args>
        </button>

      </children>
    </table>

  </children>
</vertgroup>';
}

$gMain_disp->addEvent(
    'showcat',
    'main_showcat'
    );
function main_showcat(
    $eventData
    )
{
    global $gXml_def, $gLocale, $gPage_title;

    $faq_cat = new InnoworkFaqCategory(
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess(),
        $eventData['id']
        );
    $cat_data = $faq_cat->getItem();

    $gXml_def =
'<horizgroup>
  <children>

    <table>
      <args>
        <headers type="array">'.WuiXml::encode(
            array( '0' => array(
                'label' => $gLocale->getStr( 'category.label' )
                ) ) ).'</headers>
      </args>
      <children>';

    if ( $eventData['id'] != '0' )
        $gXml_def .=
'    <form row="0" col="0"><name>category</name>
      <args>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'showcat',
                    array(
                        'id' => $eventData['id'],
                        'iframe' => '1'
                        )
                    ),
                array(
                    'action',
                    'editcat',
                    array(
                        'id' => $eventData['id']
                        )
                    )
            ) ) ).'</action>
      </args>
      <children>

        <horizgroup>
          <args>
            <align>middle</align>
          </args>
          <children>

            <string><name>title</name>
              <args>
                <disp>action</disp>
                <size>35</size>
                <value type="encoded">'.urlencode( $cat_data['title'] ).'</value>
              </args>
            </string>

            <button>
          <args>
            <themeimage>buttonok</themeimage>
            <horiz>true</horiz>
            <frame>false</frame>
            <label type="encoded">'.urlencode( $gLocale->getStr( 'apply.button' ) ).'</label>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'showcat',
                    array(
                        'id' => $eventData['id'],
                        'iframe' => '1'
                        )
                    ),
                array(
                    'action',
                    'editcat',
                    array(
                        'id' => $eventData['id']
                        )
                    )
            ) ) ).'</action>
          </args>
            </button>

        <button>
          <args>
            <themeimage>editcut</themeimage>
            <horiz>true</horiz>
            <frame>false</frame>
            <label type="encoded">'.urlencode( $gLocale->getStr( 'cut.button' ) ).'</label>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'showcat',
                    array(
                        'id' => $eventData['id'],
                        'iframe' => $eventData['iframe']
                        )
                    ),
                array(
                    'action',
                    'cut',
                    array(
                        'type' => 'category',
                        'id' => $eventData['id']
                        ) )
            ) ) ).'</action>
          </args>
        </button>

        <button>
          <args>
            <themeimage>trash</themeimage>
            <horiz>true</horiz>
            <frame>false</frame>
            <needconfirm>true</needconfirm>
            <confirmmessage type="encoded">'.urlencode( $gLocale->getStr( 'trash.confirm' ) ).'</confirmmessage>
            <label type="encoded">'.urlencode( $gLocale->getStr( 'trash.button' ) ).'</label>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'showcat',
                    array(
                        'id' => $cat_data['parentid'],
                        'iframe' => $eventData['iframe']
                        )
                    ),
                array(
                    'action',
                    'removecat',
                    array(
                        'id' => $eventData['id']
                        ) )
            ) ) ).'</action>
          </args>
        </button>

          </children>
        </horizgroup>

      </children>
    </form>';

    $gXml_def .=
'    <horizgroup row="1" col="0">
      <children>';

        require_once('innomatic/datatransfer/Clipboard.php');

        $clip = new Clipboard(
            Clipboard::TYPE_ARRAY,
            '',
            0,
            'innowork-faq',
            \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDomainId(),
            \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserName()
            );

        if ( $clip->IsValid() ) {
            $gXml_def .=
'            <button>
              <args>
                <themeimage>editpaste</themeimage>
                <horiz>true</horiz>
                <frame>false</frame>
                <label type="encoded">'.urlencode( $gLocale->getStr( 'paste.button' ) ).'</label>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'showcat',
                    array(
                        'id' => $eventData['id'],
                        'iframe' => $eventData['iframe']
                        )
                    ),
                array(
                    'action',
                    'paste',
                    array(
                        'id' => $eventData['id']
                        ) )
            ) ) ).'</action>
              </args>
            </button>';
        }

    $gXml_def .=
'        <button>
          <args>
            <themeimage>filenew</themeimage>
            <horiz>true</horiz>
            <frame>false</frame>
            <label type="encoded">'.urlencode( $gLocale->getStr( 'newcategory.button' ) ).'</label>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'newcategory',
                    array(
                        'parentid' => $eventData['id'],
                        'iframe' => '1'
                        )
                    )
            ) ) ).'</action>
          </args>
        </button>

        <button>
          <args>
            <themeimage>filenew</themeimage>
            <horiz>true</horiz>
            <frame>false</frame>
            <label type="encoded">'.urlencode( $gLocale->getStr( 'newfaq.button' ) ).'</label>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'newfaq',
                    array(
                        'parentid' => $eventData['id'],
                        'iframe' => '1'
                        )
                    )
            ) ) ).'</action>
          </args>
        </button>

      </children>
    </horizgroup>

        <table row="2" col="0">
          <args>
        <headers type="array">'.WuiXml::encode(
            array( '0' => array(
                'label' => $gLocale->getStr( 'items.label' )
                ) ) ).'</headers>
          </args>
          <children>';

    $row = 0;

    $faq_cat = new InnoworkFaqCategory(
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess()
        );
    $cat_search = $faq_cat->Search(
        array(
            'parentid' => $eventData['id']
            ),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId()
        );

    foreach ( $cat_search as $id => $data ) {
        $gXml_def .=
'<button row="'.$row.'" col="0">
  <args>
    <themeimage>folder</themeimage>
    <themeimagetype>mini</themeimagetype>
    <action type="encoded">'.urlencode(
        WuiEventsCall::buildEventsCallString(
            '',
            array(
                array(
                    'view',
                    'showcat',
                    array(
                        'id' => $id,
                        'iframe' => '1'
                        )
                    )
                )
            )
        ).'</action>
  </args>
</button>
<link row="'.$row.'" col="1">
  <args>
    <link type="encoded">'.urlencode(
        WuiEventsCall::buildEventsCallString(
            '',
            array(
                array(
                    'view',
                    'showcat',
                    array(
                        'id' => $id,
                        'iframe' => '1'
                        )
                    )
                )
            )
        ).'</link>
    <label type="encoded">'.urlencode( $data['title'] ).'</label>
  </args>
</link>';
        $row++;
    }

    $faq_node = new InnoworkFaqNode(
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess()
        );
    $nodes_search = $faq_node->Search(
        array(
            'parentid' => $eventData['id']
            ),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserId()
        );

    foreach ( $nodes_search as $id => $data ) {
        $gXml_def .=
'<button row="'.$row.'" col="0">
  <args>
    <themeimage>document</themeimage>
    <themeimagetype>mini</themeimagetype>
    <action type="encoded">'.urlencode(
        WuiEventsCall::buildEventsCallString(
            '',
            array(
                array(
                    'view',
                    'showfaq',
                    array(
                        'id' => $id,
                        'iframe' => '1'
                        )
                    )
                )
            )
        ).'</action>
  </args>
</button>
<link row="'.$row.'" col="1">
  <args>
    <link type="encoded">'.urlencode(
        WuiEventsCall::buildEventsCallString(
            '',
            array(
                array(
                    'view',
                    'showfaq',
                    array(
                        'id' => $id,
                        'iframe' => '1'
                        )
                    )
                )
            )
        ).'</link>
    <label type="encoded">'.urlencode( $data['question'] ).'</label>
  </args>
</link>';

        $row++;
    }

    $gXml_def .=
'          </children>
        </table>

      </children>
    </table>

  <innoworkitemacl><name>itemacl</name>
    <args>
      <itemtype>faqcategory</itemtype>
      <itemid>'.$cat_data['id'].'</itemid>
      <itemownerid>'.$cat_data['ownerid'].'</itemownerid>
      <defaultaction type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
        array( 'view', 'showcat', array( 'id' => $eventData['id'], 'iframe' => $eventData['iframe'] ) ) ) ) ).'</defaultaction>
    </args>
  </innoworkitemacl>

  </children>
</horizgroup>';
}

$gMain_disp->addEvent(
    'newfaq',
    'main_newfaq'
    );
function main_newfaq(
    $eventData
    )
{
    global $gXml_def, $gLocale, $gPage_title;

    $gXml_def =
'<vertgroup>
  <children>

    <table>
      <args>
        <headers type="array">'.WuiXml::encode(
            array( '0' => array(
                'label' => $gLocale->getStr( 'newfaq.label' )
                ) ) ).'</headers>
      </args>
      <children>

    <form row="0" col="0"><name>faq</name>
      <args>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'showcat',
                    array(
                        'id' => $eventData['parentid'],
                        'iframe' => $eventData['iframe']
                        )
                    ),
                array(
                    'action',
                    'newfaq',
                    array(
                        'parentid' => $eventData['parentid']
                        ) )
            ) ) ).'</action>
      </args>
      <children>

        <grid>
          <children>

            <label row="0" col="0">
              <args>
                <label type="encoded">'.urlencode( $gLocale->getStr( 'question.label' ) ).'</label>
              </args>
            </label>
            <text row="1" col="0"><name>question</name>
              <args>
                <disp>action</disp>
                <rows>3</rows>
                <cols>50</cols>
              </args>
            </text>

            <label row="2" col="0">
              <args>
                <label type="encoded">'.urlencode( $gLocale->getStr( 'answer.label' ) ).'</label>
              </args>
            </label>
            <text row="3" col="0"><name>answer</name>
              <args>
                <disp>action</disp>
                <rows>10</rows>
                <cols>50</cols>
              </args>
            </text>

          </children>
        </grid>

      </children>
    </form>

        <button row="1" col="0"><name>apply</name>
          <args>
            <themeimage>buttonok</themeimage>
            <horiz>true</horiz>
            <frame>false</frame>
            <formsubmit>faq</formsubmit>
            <label type="encoded">'.urlencode( $gLocale->getStr( 'apply.button' ) ).'</label>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'showcat',
                    array(
                        'id' => $eventData['parentid'],
                        'iframe' => $eventData['iframe']
                        )
                    ),
                array(
                    'action',
                    'newfaq',
                    array(
                        'parentid' => $eventData['parentid']
                        ) )
            ) ) ).'</action>
          </args>
        </button>

      </children>
    </table>

  </children>
</vertgroup>';
}

$gMain_disp->addEvent(
    'showfaq',
    'main_showfaq'
    );
function main_showfaq(
    $eventData
    )
{
    global $gXml_def, $gLocale, $gPage_title;

    $faq_node = new InnoworkFaqNode(
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess(),
        $eventData['id']
        );
    $faq_data = $faq_node->getItem();

    $gXml_def =
'<horizgroup>
  <children>

    <table>
      <args>
        <headers type="array">'.WuiXml::encode(
            array( '0' => array(
                'label' => $gLocale->getStr( 'faq.label' )
                ) ) ).'</headers>
      </args>
      <children>

    <form row="0" col="0"><name>faq</name>
      <args>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'showcat',
                    array(
                        'id' => $faq_data['parentid'],
                        'parentid' => $faq_data['parentid'],
                        'iframe' => $eventData['iframe']
                        )
                    ),
                array(
                    'action',
                    'editfaq',
                    array(
                        'id' => $eventData['id']
                        ) )
            ) ) ).'</action>
      </args>
      <children>

        <grid>
          <children>

            <label row="0" col="0">
              <args>
                <label type="encoded">'.urlencode( $gLocale->getStr( 'question.label' ) ).'</label>
              </args>
            </label>
            <text row="1" col="0"><name>question</name>
              <args>
                <disp>action</disp>
                <rows>3</rows>
                <cols>50</cols>
                <value type="encoded">'.urlencode( $faq_data['question'] ).'</value>
              </args>
            </text>

            <label row="2" col="0">
              <args>
                <label type="encoded">'.urlencode( $gLocale->getStr( 'answer.label' ) ).'</label>
              </args>
            </label>
            <text row="3" col="0"><name>answer</name>
              <args>
                <disp>action</disp>
                <rows>10</rows>
                <cols>50</cols>
                <value type="encoded">'.urlencode( $faq_data['answer'] ).'</value>
              </args>
            </text>

          </children>
        </grid>

      </children>
    </form>

    <horizgroup row="1" col="0">
      <children>

        <button><name>apply</name>
          <args>
            <themeimage>buttonok</themeimage>
            <horiz>true</horiz>
            <frame>false</frame>
            <formsubmit>faq</formsubmit>
            <label type="encoded">'.urlencode( $gLocale->getStr( 'apply.button' ) ).'</label>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'showcat',
                    array(
                        'id' => $faq_data['parentid'],
                        'parentid' => $faq_data['parentid'],
                        'iframe' => $eventData['iframe']
                        )
                    ),
                array(
                    'action',
                    'editfaq',
                    array(
                        'id' => $eventData['id']
                        ) )
            ) ) ).'</action>
          </args>
        </button>

        <button>
          <args>
            <themeimage>fileclose</themeimage>
            <horiz>true</horiz>
            <frame>false</frame>
            <label type="encoded">'.urlencode( $gLocale->getStr( 'close.button' ) ).'</label>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'showcat',
                    array(
                        'id' => $faq_data['parentid'],
                        'parentid' => $faq_data['parentid'],
                        'iframe' => $eventData['iframe']
                        )
                    )
            ) ) ).'</action>
          </args>
        </button>

        <button>
          <args>
            <themeimage>editcut</themeimage>
            <horiz>true</horiz>
            <frame>false</frame>
            <label type="encoded">'.urlencode( $gLocale->getStr( 'cut.button' ) ).'</label>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'showcat',
                    array(
                        'id' => $faq_data['parentid'],
                        'parentid' => $faq_data['parentid'],
                        'iframe' => $eventData['iframe']
                        )
                    ),
                array(
                    'action',
                    'cut',
                    array(
                        'type' => 'node',
                        'id' => $eventData['id']
                        ) )
            ) ) ).'</action>
          </args>
        </button>

        <button>
          <args>
            <themeimage>trash</themeimage>
            <horiz>true</horiz>
            <frame>false</frame>
            <needconfirm>true</needconfirm>
            <confirmmessage type="encoded">'.urlencode( $gLocale->getStr( 'trash.confirm' ) ).'</confirmmessage>
            <label type="encoded">'.urlencode( $gLocale->getStr( 'trash.button' ) ).'</label>
            <action type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
                array(
                    'view',
                    'showcat',
                    array(
                        'id' => $faq_data['parentid'],
                        'parentid' => $faq_data['parentid'],
                        'iframe' => $eventData['iframe']
                        )
                    ),
                array(
                    'action',
                    'removefaq',
                    array(
                        'id' => $eventData['id']
                        ) )
            ) ) ).'</action>
          </args>
        </button>

        </children>
        </horizgroup>

      </children>
    </table>

  <innoworkitemacl><name>itemacl</name>
    <args>
      <itemtype>faqnode</itemtype>
      <itemid>'.$eventData['id'].'</itemid>
      <itemownerid>'.$faq_data['ownerid'].'</itemownerid>
      <defaultaction type="encoded">'.urlencode( WuiEventsCall::buildEventsCallString( '', array(
        array( 'view', 'showfaq', array( 'id' => $eventData['id'], 'iframe' => $eventData['iframe'] ) ) ) ) ).'</defaultaction>
    </args>
  </innoworkitemacl>

  </children>
</horizgroup>';
}

$gMain_disp->Dispatch();

// ----- Rendering -----
//
$main_event_data = $gMain_disp->getEventData();

if (
    isset($main_event_data['iframe'] )
    and
    $main_event_data['iframe'] == '1'
    )
{

    $gXml_def =
'<page>
  <args>
    <border>false</border>
  </args>
  <children>
    <vertgroup>
      <children>'.$gXml_def;

    if ( strlen( $gPage_status ) )
        $gXml_def .=
'        <statusbar>
          <args>
            <status type="encoded">'.urlencode( $gPage_status ).'</status>
          </args>
        </statusbar>';

    $gXml_def .=
'      </children>
    </vertgroup>
  </children>
</page>';

    $wui = new WuiXml( '', array( 'definition' => $gXml_def ) );
    $wui->build($gWui->getDispatcher());
    echo $wui->render();
} else {
    $gWui->addChild( new WuiInnomaticPage( 'page', array(
        'pagetitle' => $gPage_title,
        'icon' => 'info',
        'toolbars' => array(
            new WuiInnomaticToolbar(
                'view',
                array(
                    'toolbars' => $gToolbars, 'toolbar' => 'true'
                    ) ),
            new WuiInnomaticToolBar(
                'core',
                array(
                    'toolbars' => $gCore_toolbars, 'toolbar' => 'true'
                    ) )
                ),
        'maincontent' => new WuiXml(
            'page', array(
                'definition' => $gXml_def
                ) ),
        'status' => $gPage_status
        ) ) );

    $gWui->render();
}
