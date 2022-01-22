<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211215172857 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auteur (id INT AUTO_INCREMENT NOT NULL, nom_prenom VARCHAR(100) NOT NULL, sexe TINYINT(1) NOT NULL, date_de_naissance DATE NOT NULL, nationalite VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE auteur_livre (auteur_id INT NOT NULL, livre_id INT NOT NULL, INDEX IDX_A6DFA5E060BB6FE6 (auteur_id), INDEX IDX_A6DFA5E037D925CB (livre_id), PRIMARY KEY(auteur_id, livre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genre (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genre_livre (genre_id INT NOT NULL, livre_id INT NOT NULL, INDEX IDX_1165505C4296D31F (genre_id), INDEX IDX_1165505C37D925CB (livre_id), PRIMARY KEY(genre_id, livre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE livre (id INT AUTO_INCREMENT NOT NULL, isbn VARCHAR(25) NOT NULL, titre VARCHAR(255) NOT NULL, nbpages INT NOT NULL, date_de_parution DATE NOT NULL, note INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE auteur_livre ADD CONSTRAINT FK_A6DFA5E060BB6FE6 FOREIGN KEY (auteur_id) REFERENCES auteur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE auteur_livre ADD CONSTRAINT FK_A6DFA5E037D925CB FOREIGN KEY (livre_id) REFERENCES livre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE genre_livre ADD CONSTRAINT FK_1165505C4296D31F FOREIGN KEY (genre_id) REFERENCES genre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE genre_livre ADD CONSTRAINT FK_1165505C37D925CB FOREIGN KEY (livre_id) REFERENCES livre (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auteur_livre DROP FOREIGN KEY FK_A6DFA5E060BB6FE6');
        $this->addSql('ALTER TABLE genre_livre DROP FOREIGN KEY FK_1165505C4296D31F');
        $this->addSql('ALTER TABLE auteur_livre DROP FOREIGN KEY FK_A6DFA5E037D925CB');
        $this->addSql('ALTER TABLE genre_livre DROP FOREIGN KEY FK_1165505C37D925CB');
        $this->addSql('DROP TABLE auteur');
        $this->addSql('DROP TABLE auteur_livre');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE genre_livre');
        $this->addSql('DROP TABLE livre');
    }
}
