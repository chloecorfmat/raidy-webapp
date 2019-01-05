<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181230184429 extends AbstractMigration
{
    protected static $class = 'AppKernel';
    protected static $kernel;

    /**
     * Creates a Kernel.
     *
     * Available options:
     *
     *  * environment
     *  * debug
     *
     * @param array $options An array of options
     *
     * @return HttpKernelInterface A HttpKernelInterface instance
     */
    protected static function createKernel(array $options = array())
    {
        if (null === static::$class) {
            static::$class = static::getKernelClass();
        }

        return new static::$class(
            isset($options['environment']) ? $options['environment'] : 'test',
            isset($options['debug']) ? $options['debug'] : true
        );
    }

    /**
     * Creates a Client.
     *
     * @param array $options An array of options to pass to the createKernel class
     * @param array $server  An array of server parameters
     *
     * @return Client A Client instance
     */
    protected static function createClient(array $options = array(), array $server = array())
    {
        if (null !== static::$kernel) {
            static::$kernel->shutdown();
        }

        static::$kernel = static::createKernel($options);
        static::$kernel->boot();

        $client = static::$kernel->getContainer()->get('test.client');
        $client->setServerParameters($server);

        return $client;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE helper ADD accept_conditions DATETIME');
    }

    public function postUp(Schema $schema)
    {
        $this->client = self::createClient();
        $this->em = $this->client->getKernel()->getContainer()->get('doctrine')->getEntityManager();

        $helpers = $this->em->getRepository('AppBundle:Helper')->findAll();
        foreach($helpers as $helper){
            // need this so we force the generation of a datetime.
            $helper->setAcceptConditions(new \DateTime("now"));
            $this->em->persist($helper);
        }
        $this->em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
