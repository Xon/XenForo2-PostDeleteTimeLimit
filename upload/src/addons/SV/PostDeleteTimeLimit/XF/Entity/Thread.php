<?php

namespace SV\PostDeleteTimeLimit\XF\Entity;

/**
 * Extends \XF\Entity\Thread
 */
class Thread extends XFCP_Thread
{
    public function canDelete($type = 'soft', &$error = null)
    {
        $canDelete = parent::canDelete($type, $error);
        if (!$canDelete)
        {
            return false;
        }

        $visitor = \XF::visitor();
        $nodeId = $this->node_id;

        if ($visitor->hasNodePermission($nodeId, 'deleteAnyThread'))
        {
            return true;
        }

        $deleteLimit = $visitor->hasNodePermission($nodeId, 'deleteOwnPostTimeLimit');

        if ($deleteLimit != -1 && (!$deleteLimit || $this->post_date < \XF::$time - 60 * $deleteLimit))
        {
            $error = \XF::phraseDeferred('message_edit_time_limit_expired', ['minutes' => $deleteLimit]);

            return false;
        }

        return true;
    }
}