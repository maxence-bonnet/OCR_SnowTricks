<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211207124924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89B281BE2E');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91ED6BDC9DC');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91ED6BDC9DC FOREIGN KEY (main_picture_id) REFERENCES picture (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89B281BE2E');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91ED6BDC9DC');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91ED6BDC9DC FOREIGN KEY (main_picture_id) REFERENCES picture (id)');
    }
}
