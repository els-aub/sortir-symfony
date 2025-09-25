<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250925124506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE etats (id_etat INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(30) NOT NULL, PRIMARY KEY(id_etat)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inscriptions (sorties_id_sortie INT NOT NULL, participants_id_participant INT NOT NULL, date_inscription DATETIME NOT NULL, INDEX IDX_74E0281CFE3A89BC (sorties_id_sortie), INDEX IDX_74E0281C6D718B64 (participants_id_participant), PRIMARY KEY(sorties_id_sortie, participants_id_participant)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lieux (id_lieu INT AUTO_INCREMENT NOT NULL, villes_id_ville INT NOT NULL, nom_lieu VARCHAR(30) NOT NULL, rue VARCHAR(30) DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, INDEX IDX_9E44A8AE319DF220 (villes_id_ville), PRIMARY KEY(id_lieu)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participants (id_participant INT AUTO_INCREMENT NOT NULL, sites_id_site INT NOT NULL, user_id INT NOT NULL, pseudo VARCHAR(30) NOT NULL, nom VARCHAR(30) NOT NULL, prenom VARCHAR(30) NOT NULL, telephone VARCHAR(15) DEFAULT NULL, mail VARCHAR(20) NOT NULL, mot_de_passe VARCHAR(20) NOT NULL, administrateur TINYINT(1) NOT NULL, actif TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_7169709286CC499D (pseudo), INDEX IDX_71697092FEA33AF6 (sites_id_site), UNIQUE INDEX UNIQ_71697092A76ED395 (user_id), PRIMARY KEY(id_participant)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sites (id_site INT AUTO_INCREMENT NOT NULL, nom_site VARCHAR(30) NOT NULL, PRIMARY KEY(id_site)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sorties (id_sortie INT AUTO_INCREMENT NOT NULL, id_participant INT NOT NULL, id_lieu INT NOT NULL, id_etat INT NOT NULL, nom VARCHAR(30) NOT NULL, datedebut DATETIME NOT NULL, duree INT DEFAULT NULL, datecloture DATETIME NOT NULL, nbinscriptionsmax INT NOT NULL, descriptioninfos VARCHAR(500) DEFAULT NULL, urlPhoto VARCHAR(250) DEFAULT NULL, INDEX IDX_488163E8CF8DA6E6 (id_participant), INDEX IDX_488163E8A477615B (id_lieu), INDEX IDX_488163E8DEEAEB60 (id_etat), PRIMARY KEY(id_sortie)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE villes (id_ville INT AUTO_INCREMENT NOT NULL, nom_ville VARCHAR(30) NOT NULL, code_postal VARCHAR(10) NOT NULL, PRIMARY KEY(id_ville)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inscriptions ADD CONSTRAINT FK_74E0281CFE3A89BC FOREIGN KEY (sorties_id_sortie) REFERENCES sorties (id_sortie)');
        $this->addSql('ALTER TABLE inscriptions ADD CONSTRAINT FK_74E0281C6D718B64 FOREIGN KEY (participants_id_participant) REFERENCES participants (id_participant)');
        $this->addSql('ALTER TABLE lieux ADD CONSTRAINT FK_9E44A8AE319DF220 FOREIGN KEY (villes_id_ville) REFERENCES villes (id_ville)');
        $this->addSql('ALTER TABLE participants ADD CONSTRAINT FK_71697092FEA33AF6 FOREIGN KEY (sites_id_site) REFERENCES sites (id_site)');
        $this->addSql('ALTER TABLE participants ADD CONSTRAINT FK_71697092A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E8CF8DA6E6 FOREIGN KEY (id_participant) REFERENCES participants (id_participant)');
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E8A477615B FOREIGN KEY (id_lieu) REFERENCES lieux (id_lieu)');
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E8DEEAEB60 FOREIGN KEY (id_etat) REFERENCES etats (id_etat)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inscriptions DROP FOREIGN KEY FK_74E0281CFE3A89BC');
        $this->addSql('ALTER TABLE inscriptions DROP FOREIGN KEY FK_74E0281C6D718B64');
        $this->addSql('ALTER TABLE lieux DROP FOREIGN KEY FK_9E44A8AE319DF220');
        $this->addSql('ALTER TABLE participants DROP FOREIGN KEY FK_71697092FEA33AF6');
        $this->addSql('ALTER TABLE participants DROP FOREIGN KEY FK_71697092A76ED395');
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E8CF8DA6E6');
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E8A477615B');
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E8DEEAEB60');
        $this->addSql('DROP TABLE etats');
        $this->addSql('DROP TABLE inscriptions');
        $this->addSql('DROP TABLE lieux');
        $this->addSql('DROP TABLE participants');
        $this->addSql('DROP TABLE sites');
        $this->addSql('DROP TABLE sorties');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE villes');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
