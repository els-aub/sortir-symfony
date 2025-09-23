<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250923081249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ETATS (no_etat INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(30) NOT NULL, PRIMARY KEY(no_etat)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE INSCRIPTIONS (sorties_no_sortie INT NOT NULL, participants_no_participant INT NOT NULL, date_inscription DATETIME NOT NULL, INDEX IDX_E1D2610CC731F823 (sorties_no_sortie), INDEX IDX_E1D2610CEF759E07 (participants_no_participant), PRIMARY KEY(sorties_no_sortie, participants_no_participant)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE LIEUX (no_lieu INT AUTO_INCREMENT NOT NULL, villes_no_ville INT NOT NULL, nom_lieu VARCHAR(30) NOT NULL, rue VARCHAR(30) DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, INDEX IDX_69106A1E395FAFC3 (villes_no_ville), PRIMARY KEY(no_lieu)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE PARTICIPANTS (no_participant INT AUTO_INCREMENT NOT NULL, sites_no_site INT NOT NULL, pseudo VARCHAR(30) NOT NULL, nom VARCHAR(30) NOT NULL, prenom VARCHAR(30) NOT NULL, telephone VARCHAR(15) DEFAULT NULL, mail VARCHAR(20) NOT NULL, mot_de_passe VARCHAR(20) NOT NULL, administrateur TINYINT(1) NOT NULL, actif TINYINT(1) NOT NULL, INDEX IDX_E45B398251C3F4BB (sites_no_site), UNIQUE INDEX participants_pseudo_uk (pseudo), PRIMARY KEY(no_participant)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE SITES (no_site INT AUTO_INCREMENT NOT NULL, nom_site VARCHAR(30) NOT NULL, PRIMARY KEY(no_site)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE SORTIES (no_sortie INT AUTO_INCREMENT NOT NULL, organisateur INT NOT NULL, lieux_no_lieu INT NOT NULL, etats_no_etat INT NOT NULL, nom VARCHAR(30) NOT NULL, datedebut DATETIME NOT NULL, duree INT DEFAULT NULL, datecloture DATETIME NOT NULL, nbinscriptionsmax INT NOT NULL, descriptioninfos VARCHAR(500) DEFAULT NULL, etatsortie INT DEFAULT NULL, urlPhoto VARCHAR(250) DEFAULT NULL, INDEX IDX_77C49DF24BD76D44 (organisateur), INDEX IDX_77C49DF24E23F7D7 (lieux_no_lieu), INDEX IDX_77C49DF2FCD21D77 (etats_no_etat), PRIMARY KEY(no_sortie)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE VILLES (no_ville INT AUTO_INCREMENT NOT NULL, nom_ville VARCHAR(30) NOT NULL, code_postal VARCHAR(10) NOT NULL, PRIMARY KEY(no_ville)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE INSCRIPTIONS ADD CONSTRAINT FK_E1D2610CC731F823 FOREIGN KEY (sorties_no_sortie) REFERENCES SORTIES (no_sortie)');
        $this->addSql('ALTER TABLE INSCRIPTIONS ADD CONSTRAINT FK_E1D2610CEF759E07 FOREIGN KEY (participants_no_participant) REFERENCES PARTICIPANTS (no_participant)');
        $this->addSql('ALTER TABLE LIEUX ADD CONSTRAINT FK_69106A1E395FAFC3 FOREIGN KEY (villes_no_ville) REFERENCES VILLES (no_ville)');
        $this->addSql('ALTER TABLE PARTICIPANTS ADD CONSTRAINT FK_E45B398251C3F4BB FOREIGN KEY (sites_no_site) REFERENCES SITES (no_site)');
        $this->addSql('ALTER TABLE SORTIES ADD CONSTRAINT FK_77C49DF24BD76D44 FOREIGN KEY (organisateur) REFERENCES PARTICIPANTS (no_participant)');
        $this->addSql('ALTER TABLE SORTIES ADD CONSTRAINT FK_77C49DF24E23F7D7 FOREIGN KEY (lieux_no_lieu) REFERENCES LIEUX (no_lieu)');
        $this->addSql('ALTER TABLE SORTIES ADD CONSTRAINT FK_77C49DF2FCD21D77 FOREIGN KEY (etats_no_etat) REFERENCES ETATS (no_etat)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE INSCRIPTIONS DROP FOREIGN KEY FK_E1D2610CC731F823');
        $this->addSql('ALTER TABLE INSCRIPTIONS DROP FOREIGN KEY FK_E1D2610CEF759E07');
        $this->addSql('ALTER TABLE LIEUX DROP FOREIGN KEY FK_69106A1E395FAFC3');
        $this->addSql('ALTER TABLE PARTICIPANTS DROP FOREIGN KEY FK_E45B398251C3F4BB');
        $this->addSql('ALTER TABLE SORTIES DROP FOREIGN KEY FK_77C49DF24BD76D44');
        $this->addSql('ALTER TABLE SORTIES DROP FOREIGN KEY FK_77C49DF24E23F7D7');
        $this->addSql('ALTER TABLE SORTIES DROP FOREIGN KEY FK_77C49DF2FCD21D77');
        $this->addSql('DROP TABLE ETATS');
        $this->addSql('DROP TABLE INSCRIPTIONS');
        $this->addSql('DROP TABLE LIEUX');
        $this->addSql('DROP TABLE PARTICIPANTS');
        $this->addSql('DROP TABLE SITES');
        $this->addSql('DROP TABLE SORTIES');
        $this->addSql('DROP TABLE VILLES');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
