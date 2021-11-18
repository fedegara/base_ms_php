<?php
/**
 * Created by PhpStorm.
 * User: dabreu
 * Date: 2/1/21
 * Time: 1:54 p. m.
 */

namespace App\Application\Actions;

use App\Domain\DTO\Utils\Cipher\GPG;
use App\Domain\DTO\Utils\Cipher\SSL;
use App\Infraestructure\Adapters\QueueAdapter;
use App\Infraestructure\Persistence\Mongo\MongoConnection;
use Cratia\Rest\Actions\Action;
use Exception;
use Slim\Exception\HttpException;

class Status extends Action
{
    private $test_content = "Hi. This is a test for all tests sections";

    /**
     * @inheritDoc
     */
    protected function action()
    {

       $mongoStatus = $this->testMongoDB();
        return [
            'MongoDB' => $mongoStatus === true ? 'OK' : $mongoStatus
        ];
    }

    public function testMongoDB()
    {

        try{
            /** @var MongoConnection $mongo_connection */
            $mongo_connection = $this->getContainer()->get(MongoConnection::class);
            $mongo_connection->listCollections();
            return true;
        }catch (Exception $ex){
            throw new Exception("MongoDB: ERROR -> [EXCEPTION] {$ex->getMessage()}");

        }

    }


}