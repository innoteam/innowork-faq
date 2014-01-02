<?php
require_once('innowork/core/InnoworkItem.php');

class InnoworkFaqCategory extends InnoworkItem
{
    public $mTable = 'innowork_faq_categories';
    public $mNewDispatcher = '';
    public $mNewEvent = '';
    public $mShowDispatcher = 'view';
    public $mShowEvent = 'showcat';
    public $mNoTrash = false;

    public function __construct(
        $rrootDb,
        $rdomainDA,
        $faqId = 0
        )
    {
        parent::__construct(
            $rrootDb,
            $rdomainDA,
            'faqcategory',
            $faqId
            );

        $this->mKeys['title'] = 'text';
        $this->mKeys['description'] = 'text';
        $this->mKeys['parentid'] = 'integer';

        $this->mSearchResultKeys[] = 'title';
        $this->mSearchResultKeys[] = 'description';
        $this->mSearchResultKeys[] = 'parentid';

        $this->mViewableSearchResultKeys[] = 'title';
        $this->mViewableSearchResultKeys[] = 'description';

        $this->mSearchOrderBy = 'title';
    }

    public function doCreate(
        $params,
        $userId
        )
    {
        $result = false;

        $params['trashed'] = $this->mrDomainDA->fmtfalse;
        if ( !isset($params['parentid'] ) ) $params['parentid'] = 0;

        if ( count( $params ) ) {


            $item_id = $this->mrDomainDA->getNextSequenceValue( $this->mTable.'_id_seq' );

            $key_pre = $value_pre = $keys = $values = '';

            while ( list( $key, $val ) = each( $params ) ) {
                $key_pre = ',';
                $value_pre = ',';

                switch ( $key ) {
                case 'title':
                case 'description':
                case 'trashed':
                    $keys .= $key_pre.$key;
                    $values .= $value_pre.$this->mrDomainDA->formatText( $val );
                    break;

                case 'parentid':
                    if ( !strlen( $key ) ) $key = 0;
                    $keys .= $key_pre.$key;
                    $values .= $value_pre.$val;
                    break;

                default:
                    break;
                }
            }

            if ( strlen( $values ) ) {
                if ( $this->mrDomainDA->Execute( 'INSERT INTO '.$this->mTable.' '.
                                               '(id,ownerid'.$keys.') '.
                                               'VALUES ('.$item_id.','.
                                               $userId.
                                               $values.')' ) )
                {
                    $result = $item_id;
                }
            }
        }

        return $result;
    }

    public function doEdit(
        $params
        )
    {
        $result = false;

        if ( $this->mItemId ) {
            if ( count( $params ) ) {
                $start = 1;
                $update_str = '';

                while ( list( $field, $value ) = each( $params ) ) {
                    if ( $field != 'id' ) {
                        switch ( $field ) {
                        case 'title':
                        case 'description':
                        case 'trashed':
                            if ( !$start ) $update_str .= ',';
                            $update_str .= $field.'='.$this->mrDomainDA->formatText( $value );
                            $start = 0;
                            break;

                        case 'parentid':
                            if ( !strlen( $value ) ) $value = 0;
                            if ( !$start ) $update_str .= ',';
                            $update_str .= $field.'='.$value;
                            $start = 0;
                            break;

                        default:
                            break;
                        }
                    }
                }

                $query = &$this->mrDomainDA->Execute(
                    'UPDATE '.$this->mTable.' '.
                    'SET '.$update_str.' '.
                    'WHERE id='.$this->mItemId );

                if ( $query ) $result = true;
            }
        }

        return $result;
    }

    public function doRemove(
        $userId
        )
    {
        $result = false;

        $all_removed = true;

        // Categories

        $cats_query = \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess()->Execute(
            'SELECT id '.
            'FROM innowork_faq_categories '.
            'WHERE parentid='.$this->mItemId );

        while ( !$cats_query->eof ) {
            $tmp_cat = new InnoworkFaqCategory(
                \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
                \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess(),
                $cats_query->getFields( 'id' )
                );

            if ( !$tmp_cat->Remove() ) {
                $all_removed = false;
            }
            unset( $tmp_cat );
            $cats_query->moveNext();
        }

        $cats_query->free();

        // Documents

        $faqs_query = \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess()->Execute(
            'SELECT id '.
            'FROM innowork_faq_nodes '.
            'WHERE parentid='.$this->mItemId );

        while ( !$faqs_query->eof ) {
            $tmp_faq = new InnoworkFaqNode(
                \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
                \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess(),
                $faqs_query->getFields( 'id' )
                );

            if ( !$tmp_faq->Remove() ) {
                $all_removed = false;
            }
            unset( $tmp_faq );
            $faqs_query->moveNext();
        }

        $faqs_query->free();

        if ( $all_removed ) {
            $result = $this->mrDomainDA->Execute(
                'DELETE FROM '.$this->mTable.' '.
                'WHERE id='.$this->mItemId
                );
        }

        return $result;
    }


    public function doGetItem($userId)
    {
        $result = FALSE;

        $item_query = &$this->mrDomainDA->Execute(
            'SELECT * '.
            'FROM '.$this->mTable.' '.
            'WHERE id='.$this->mItemId );

        if (
            is_object( $item_query )
            and $item_query->getNumberRows()
            )
        {
            $result = $item_query->getFields();
        }

        return $result;
    }

    public function doTrash($arg)
    {
        return true;
    }

    public function Cut()
    {
        $result = false;

        if ( $this->mItemId ) {
            require_once('innomatic/datatransfer/Clipboard.php');

            $clip = new ClipBoard(
                Clipboard::TYPE_ARRAY,
                '',
                0,
                'innowork-faq',
                \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDomainId(),
                \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserName()
                );

            $item['type'] = $this->mItemType;
            $item['id'] = $this->mItemId;
            $item['action'] = 'cut';

            $result = $clip->Store(
                $item
                );
        }

        return $result;
    }

    public function Paste()
    {
        $result = false;

            require_once('innomatic/datatransfer/Clipboard.php');

            $clip = new ClipBoard(
                Clipboard::TYPE_ARRAY,
                '',
                0,
                'innowork-faq',
                \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDomainId(),
                \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentUser()->getUserName()
                );

            if ( $clip->IsValid() ) {
                $result = $clip->Retrieve();

                if ( is_array( $result ) ) {

                    $class_name = $result['type'] == 'faqcategory' ? 'InnoworkFaqCategory' : 'InnoworkFaqNode';
                    if (!class_exists($class_name)) {
                        return false;
                    }
                    $tmp_class = new $class_name(
                        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getDataAccess(),
                        \Innomatic\Core\InnomaticContainer::instance('\Innomatic\Core\InnomaticContainer')->getCurrentDomain()->getDataAccess(),
                        $result['id']
                        );

                    $fields['parentid'] = $this->mItemId;

                    $result = $tmp_class->Edit( $fields );

                    $clip->Erase();
                } else $result = false;
            }

        return $result;
    }
}
