<?php

namespace CanalTP\NmmPortalBundle\Migrations\pdo_pgsql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version001 extends AbstractMigration
{
    const VERSION = '0.0.1';

    public function getName()
    {
        return self::VERSION;
    }

    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");

        $this->addSql('CREATE SEQUENCE t_navitia_entity_nav_nav_id_seq INCREMENT BY 1 MINVALUE 1 START 1;');
        $this->addSql('CREATE TABLE t_navitia_entity_nav (nav_id INT NOT NULL, nav_name VARCHAR(255) NOT NULL, nav_name_canonical VARCHAR(255) NOT NULL, nav_email VARCHAR(255) NOT NULL, nav_email_canonical VARCHAR(255) NOT NULL, PRIMARY KEY(nav_id));');
        $this->addSql('ALTER TABLE t_navitia_entity_nav ADD COLUMN nav_created timestamp(0) without time zone DEFAULT NOW();');
        $this->addSql('ALTER TABLE t_navitia_entity_nav ALTER COLUMN nav_created SET NOT NULL;');
        $this->addSql('ALTER TABLE t_navitia_entity_nav ADD COLUMN nav_updated timestamp(0) without time zone DEFAULT NOW();');
        $this->addSql('ALTER TABLE t_navitia_entity_nav ALTER COLUMN nav_updated SET NOT NULL;');
        $this->addSql('CREATE UNIQUE INDEX nav_email_idx ON t_navitia_entity_nav (nav_email_canonical);');

        $this->addSql('ALTER TABLE tr_customer_cus ADD column cus_navitia_entity int default null;');
        $this->addSql('ALTER TABLE tr_customer_cus ADD CONSTRAINT FK_784FEC5FDB6A43B2 FOREIGN KEY (cus_navitia_entity) REFERENCES t_navitia_entity_nav (nav_id) NOT DEFERRABLE INITIALLY IMMEDIATE;');

        $this->addSql('alter table t_perimeter_per rename column cus_id to nav_id;');
        $this->addSql('ALTER TABLE t_perimeter_per DROP CONSTRAINT fk_6b5760dabc4ee2b0;');
        //$this->addSql('CREATE UNIQUE INDEX nav_id_external_coverage_id_idx ON t_perimeter_per USING btree (nav_id, per_external_coverage_id COLLATE pg_catalog."default");');
        $this->addSql('ALTER TABLE t_perimeter_per ALTER COLUMN per_external_coverage_id DROP not NULL;');
        $this->addSql('ALTER TABLE t_perimeter_per ALTER COLUMN per_external_network_id DROP not NULL;');
        $this->addSql('CREATE INDEX IDX_6B5760DAF03A7216 ON public.t_perimeter_per (nav_id);');

        //can't keep this constraint because navitia.io's users don't necessary have coverage
        $this->addSql('DROP INDEX customer_id_external_coverage_id_external_network_id_idx;');

        $this->addSql('CREATE SEQUENCE t_navitia_token_nat_nat_id_seq INCREMENT BY 1 MINVALUE 1 START 1;');
        $this->addSql('CREATE TABLE t_navitia_token_nat (nat_id INT NOT NULL, nav_id INT DEFAULT NULL, nat_token VARCHAR(255) NOT NULL, nat_is_active BOOLEAN NOT NULL, nat_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, nat_updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(nat_id));');
        $this->addSql('CREATE INDEX IDX_356E8DADF03A7216 ON t_navitia_token_nat (nav_id);');
        $this->addSql('ALTER TABLE t_navitia_token_nat ADD CONSTRAINT FK_356E8DADF03A7216 FOREIGN KEY (nav_id) REFERENCES t_navitia_entity_nav (nav_id) NOT DEFERRABLE INITIALLY IMMEDIATE;');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE t_navitia_token_nat;');
        $this->addSql('DROP SEQUENCE t_navitia_token_nat_nat_id_seq;');

        $this->addSql('CREATE UNIQUE INDEX customer_id_external_coverage_id_external_network_id_idx ON t_perimeter_per USING btree (nav_id, per_external_coverage_id COLLATE pg_catalog."default", per_external_network_id COLLATE pg_catalog."default");');

        $this->addSql('DROP INDEX IDX_6B5760DAF03A7216;');
        $this->addSql('alter table t_perimeter_per rename column nav_id to cus_id;');
        $this->addSql('ALTER TABLE t_perimeter_per ADD CONSTRAINT fk_6b5760dabc4ee2b0 FOREIGN KEY (cus_id) REFERENCES tr_customer_cus (cus_id) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;');
        $this->addSql('ALTER TABLE t_perimeter_per ALTER COLUMN per_external_coverage_id SET not NULL;');
        $this->addSql('ALTER TABLE t_perimeter_per ALTER COLUMN per_external_network_id SET not NULL;');

        $this->addSql('ALTER TABLE tr_customer_cus DROP CONSTRAINT FK_784FEC5FDB6A43B2;');
        $this->addSql('ALTER TABLE tr_customer_cus DROP COLUMN cus_navitia_entity;');

        $this->addSql('DROP SEQUENCE t_navitia_entity_nav_nav_id_seq;');
        $this->addSql('DROP TABLE t_navitia_entity_nav CASCADE;');
    }
}
