<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210411201640 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE banners ADD url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE categorias CHANGE grupo grupo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE productos CHANGE marca_id marca_id INT DEFAULT NULL, CHANGE id_externo id_externo INT DEFAULT NULL, CHANGE descripcion descripcion VARCHAR(255) DEFAULT NULL, CHANGE precio precio DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE productos_caracteristicas CHANGE clave clave VARCHAR(255) DEFAULT NULL, CHANGE valor valor VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE usuarios CHANGE roles roles JSON NOT NULL, CHANGE nombre nombre VARCHAR(255) DEFAULT NULL, CHANGE apellido apellido VARCHAR(255) DEFAULT NULL, CHANGE telefono telefono VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE banners DROP url');
        $this->addSql('ALTER TABLE categorias CHANGE grupo grupo VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE productos CHANGE marca_id marca_id INT DEFAULT NULL, CHANGE id_externo id_externo INT DEFAULT NULL, CHANGE descripcion descripcion VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE precio precio DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE productos_caracteristicas CHANGE clave clave VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE valor valor VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE usuarios CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE nombre nombre VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE apellido apellido VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE telefono telefono VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
