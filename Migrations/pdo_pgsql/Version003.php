<?php

namespace CanalTP\NmmPortalBundle\Migrations\pdo_pgsql;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Guzzle\Http\Client;

class Version003 extends AbstractMigration implements ContainerAwareInterface
{
    const VERSION = '0.0.3';
    const NMM_ORIGIN = 'nmm';

    /*
     * @var Client
     */
    private $tyrClient;

    /*
     * @var Client
     */
    private $fenrirClient;

    /*
     * @var array
     */
    private $fenrirOrigins;

    /*
     * @var ContainerInterface
     */
    private $container;

    public function getName()
    {
        return self::VERSION;
    }

    /**
     * @param Schema $schema
     */
    public function preUp(Schema $schema)
    {
        $this->tyrClient = new Client($this->container->getParameter('nmm.tyr.url'));
        $this->fenrirClient = new Client($this->container->getParameter('nmm.fenrir.url'));
    }

    /**
     * @param Schema $schema
     */
    public function preDown(Schema $schema)
    {
        $this->tyrClient = new Client($this->container->getParameter('nmm.tyr.url'));
        $this->fenrirClient = new Client($this->container->getParameter('nmm.fenrir.url'));
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE public.tr_customer_cus ADD COLUMN fenrir_id INT NULL;');
        $this->addSql('ALTER TABLE public.tr_customer_cus ADD UNIQUE (fenrir_id);');

        /* Create NMM origin with FenrirAPI */
        $this->createNmmOrigin();

        /* Update fenrir_id field in tr_customer_cus (NMM) */
        $this->createFenrirUsersAndUpdateFenrirId();
    }

    private function createNmmOrigin()
    {
        $request = $this->fenrirClient->post("origins", [], ["name" => self::NMM_ORIGIN]);
        $json = $request->send()->getBody(true);
        $this->fenrirOrigins[self::NMM_ORIGIN] = json_decode($json, true);
    }

    private function createFenrirUsersAndUpdateFenrirId()
    {
        foreach ($this->getCustomers() as $customer) {
            $request = $this->fenrirClient->post(
                'users',
                [],
                ['username' => $customer['cus_identifier'], 'originId' => $this->fenrirOrigins[self::NMM_ORIGIN]]
            );
            $json = $request->send()->getBody(true);
            $response = json_decode($json, true);

            $this->addSql("UPDATE public.tr_customer_cus "
                . "SET fenrir_id = ".$response['id']." "
                . "WHERE cus_identifier = '".$customer['cus_identifier']."';");
        }
    }

    private function getCustomers()
    {
        $statement = $this->connection->prepare("SELECT cus_identifier FROM public.tr_customer_cus;");
        $statement->execute();

        return $statement->fetchAll();
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE public.tr_customer_cus DROP COLUMN fenrir_id;');

        foreach ($this->getFenrirUsers() as $user) {
            $request = $this->fenrirClient->delete("users/".$user['id']);
            $request->send();
        }

        $request = $this->fenrirClient->get("origins");
        $json = $request->send()->getBody(true);

        foreach (json_decode($json, true) as $origin) {
            if ($origin['name'] == self::NMM_ORIGIN) {
                $request = $this->fenrirClient->delete("origins/".$origin['id']);
                $request->send();
            }
        }
    }

    private function getFenrirUsers()
    {
        $request = $this->fenrirClient->get("users");
        $json = $request->send()->getBody(true);

        return json_decode($json, true);
    }
}
