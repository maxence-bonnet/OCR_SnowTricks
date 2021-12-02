<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211201162347 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trick_user (trick_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C320E5F9B281BE2E (trick_id), INDEX IDX_C320E5F9A76ED395 (user_id), PRIMARY KEY(trick_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trick_user ADD CONSTRAINT FK_C320E5F9B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trick_user ADD CONSTRAINT FK_C320E5F9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY picture_ibfk_1');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY trick_ibfk_1');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91ED6BDC9DC FOREIGN KEY (main_picture_id) REFERENCES picture (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE trick_user');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89B281BE2E');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT picture_ibfk_1 FOREIGN KEY (trick_id) REFERENCES trick (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91ED6BDC9DC');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT trick_ibfk_1 FOREIGN KEY (main_picture_id) REFERENCES picture (id) ON UPDATE NO ACTION ON DELETE SET NULL');
    }
}
