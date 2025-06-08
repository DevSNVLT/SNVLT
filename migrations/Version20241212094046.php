<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241212094046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE metier.import_data (id INT NOT NULL, type_fichier_id INT DEFAULT NULL, filename VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_by VARCHAR(255) NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C3A5520412928ADB ON metier.import_data (type_fichier_id)');
        $this->addSql('CREATE TABLE statut_alerte (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE type_rapport (id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE metier.import_data ADD CONSTRAINT FK_C3A5520412928ADB FOREIGN KEY (type_fichier_id) REFERENCES metier.type_document_statistique (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE metier.attribution ADD exercice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE metier.attribution ADD retire BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE metier.attribution ADD abandonne BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE metier.attribution ADD motif TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE metier.attribution ADD date_retrait TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE metier.attribution ADD date_abandon TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE metier.attribution ADD CONSTRAINT FK_442BA2AE89D40298 FOREIGN KEY (exercice_id) REFERENCES admin.exercice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_442BA2AE89D40298 ON metier.attribution (exercice_id)');
        $this->addSql('ALTER TABLE metier.documentbcbgfh DROP CONSTRAINT fk_e1052a9fcba8bd2');
        $this->addSql('ALTER TABLE metier.documentbcbgfh DROP code_reprise_id');
        $this->addSql('ALTER TABLE metier.exploitant DROP agreeboo');
        $this->addSql('ALTER TABLE metier.exportateur DROP agree');
        $this->addSql('ALTER TABLE metier.fiche_prospection ALTER date_inventaire TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE metier.inventaire_forestier ALTER date_inventaire TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE metier.lignepagecp ADD code_exercice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE metier.lignepagecp ADD CONSTRAINT FK_E26434FDC095EB28 FOREIGN KEY (code_exercice_id) REFERENCES admin.exercice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E26434FDC095EB28 ON metier.lignepagecp (code_exercice_id)');
        
        $this->addSql('ALTER TABLE metier.reprise ADD exercice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE metier.reprise ADD statut BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE metier.reprise ADD CONSTRAINT FK_951431B389D40298 FOREIGN KEY (exercice_id) REFERENCES admin.exercice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_951431B389D40298 ON metier.reprise (exercice_id)');
        $this->addSql('ALTER TABLE metier.suivi_doc ADD exercice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE metier.suivi_doc ADD CONSTRAINT FK_F9F289D40298 FOREIGN KEY (exercice_id) REFERENCES admin.exercice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F9F289D40298 ON metier.suivi_doc (exercice_id)');

    }

    public function down(Schema $schema): void
    {
		
	}
}
