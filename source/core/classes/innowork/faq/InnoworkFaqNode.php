<?php

require_once('innowork/core/InnoworkItem.php');

class InnoworkFaqNode extends InnoworkItem
{
    public $mTable = 'innowork_faq_nodes';
    /*
    public $mNewDispatcher = 'view';
    public $mNewEvent = 'newfaq';
    */
    public $mShowDispatcher = 'view';
    public $mShowEvent = 'showfaq';
    public $mNoTrash = false;
    public $mConvertible = true;

    public function __construct(
        $rrootDb,
        $rdomainDA,
        $faqNodeId = 0
        )
    {
        //$this->mParentType = 'faq';

        parent::__construct(
            $rrootDb,
            $rdomainDA,
            'faqnode',
            $faqNodeId
            );

        $this->mKeys['question'] = 'text';
        $this->mKeys['answer'] = 'text';
        $this->mKeys['parentid'] = 'integer';

        $this->mSearchResultKeys[] = 'question';
        $this->mSearchResultKeys[] = 'answer';
        $this->mSearchResultKeys[] = 'parentid';

        $this->mViewableSearchResultKeys[] = 'question';
        $this->mViewableSearchResultKeys[] = 'answer';

        $this->mSearchOrderBy = 'question';

        $this->mGenericFields['companyid'] = '';
        $this->mGenericFields['projectid'] = '';
        $this->mGenericFields['title'] = 'question';
        $this->mGenericFields['content'] = 'answer';
        $this->mGenericFields['binarycontent'] = '';

        /*
        if ( $faqNodeId ) {
            $data = $this->getItem();
            $this->mParentId = $data['faqid'];
        }
        */
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
                case 'question':
                case 'answer':
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

                    if ( $result ) {
                        $this->mParentId = $params['faqid'];
                    }
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
                        case 'question':
                        case 'answer':
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

    public function doRemove($userId)
    {
        $result = FALSE;

        $result = $this->mrDomainDA->Execute(
            'DELETE FROM '.$this->mTable.' '.
            'WHERE id='.$this->mItemId );

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

}
