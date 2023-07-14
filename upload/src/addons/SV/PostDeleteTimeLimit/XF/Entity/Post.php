<?php

namespace SV\PostDeleteTimeLimit\XF\Entity;

class Post extends XFCP_Post
{
    /**
     * @param string $type
     * @param \XF\Phrase|string|null $error
     * @return bool
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function canDelete($type = 'soft', &$error = null)
    {
        $canDelete = parent::canDelete($type, $error);
        if (!$canDelete)
        {
            return false;
        }

        $visitor = \XF::visitor();
        $nodeId = $this->Thread->node_id;

        if ($visitor->hasNodePermission($nodeId, 'deleteAnyPost'))
        {
            return true;
        }

        $deleteLimit = (int)$visitor->hasNodePermission($nodeId, 'deleteOwnPostTimeLimit');

        if ($deleteLimit !== -1 && ($deleteLimit === 0 || $this->post_date < \XF::$time - 60 * $deleteLimit))
        {
            $error = \XF::phraseDeferred('message_edit_time_limit_expired', ['minutes' => $deleteLimit]);

            return false;
        }

        return true;
    }
}