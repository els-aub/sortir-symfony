<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250924153000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Nettoyage table sorties : suppression colonne etatsortie + renommage des indexes participants/sorties';
    }

    public function up(Schema $schema): void
    {
        // Supprime la colonne obsolète
        $this->addSql('ALTER TABLE sorties DROP COLUMN etatsortie');

        // Renommage index participants
        $this->addSql('ALTER TABLE participants RENAME INDEX participants_pseudo_uk TO UNIQ_7169709286CC499D');
        $this->addSql('ALTER TABLE participants RENAME INDEX idx_e45b398251c3f4bb TO IDX_7169709251C3F4BB');

        // Renommage index sorties
        $this->addSql('ALTER TABLE sorties RENAME INDEX idx_77c49df24bd76d44 TO IDX_488163E84BD76D44');
        $this->addSql('ALTER TABLE sorties RENAME INDEX idx_77c49df24e23f7d7 TO IDX_488163E84E23F7D7');
        $this->addSql('ALTER TABLE sorties RENAME INDEX idx_77c49df2fcd21d77 TO IDX_488163E8FCD21D77');
    }

    public function down(Schema $schema): void
    {
        // On restaure la colonne si rollback
        $this->addSql('ALTER TABLE sorties ADD etatsortie INT DEFAULT NULL');

        // On restaure les anciens noms d’index
        $this->addSql('ALTER TABLE participants RENAME INDEX UNIQ_7169709286CC499D TO participants_pseudo_uk');
        $this->addSql('ALTER TABLE participants RENAME INDEX IDX_7169709251C3F4BB TO idx_e45b398251c3f4bb');

        $this->addSql('ALTER TABLE sorties RENAME INDEX IDX_488163E84BD76D44 TO idx_77c49df24bd76d44');
        $this->addSql('ALTER TABLE sorties RENAME INDEX IDX_488163E84E23F7D7 TO idx_77c49df24e23f7d7');
        $this->addSql('ALTER TABLE sorties RENAME INDEX IDX_488163E8FCD21D77 TO idx_77c49df2fcd21d77');
    }
}
