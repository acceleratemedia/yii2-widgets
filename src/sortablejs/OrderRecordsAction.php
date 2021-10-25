<?php
namespace bvb\sortablejs;

use Yii;

/**
 * A very basic action that can be implemented as the endpoint for a SortableJs
 * instance to update the order of records based on the JS list
 */
class OrderRecordsAction extends \yii\base\Action
{
    /**
     * @var string The name of the database table we are ordering records in
     */
    public $table;

    /**
     * @var string The name of the column in $table that is for ordering
     */
    public $orderColumn = 'order';

    /**
     * @var string The name of the column in $table that is the primary key
     */
    public $pkColumn = 'id';

    /**
     * @var string The name of the POST variable that contains the items in order
     */
    public $postParam = 'ids';

    /**
     * @see \yii\rest\Action::$checkAccess
     * @var callable 
     */
    public $checkAccess;

    /**
     * Update the $orderColumn in $table to match the order of records in 
     * $postParam
     * {@inheritdoc}
     */
    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        $data = Yii::$app->request->post($this->postParam);
        if(empty($data)){
            throw new BadRequestHttpException('No data passed to order columns');
        }

        $i = 0;
        foreach($data as $pkValue){
            Yii::$app->db->createCommand()->update(
                $this->table,
                [$this->orderColumn => $i],
                [$this->pkColumn => $pkValue]
            )->execute();
            $i++;
        }

        return $this->controller->asJson(['success' => true]);
    }
}