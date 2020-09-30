<?php declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200930083857 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $sql = <<<SQL
ALTER TABLE `oxvouchers`
ADD COLUMN `OXBASKETID` CHAR(32) CHARACTER SET 'latin1' COLLATE 'latin1_general_ci' NULL DEFAULT NULL COMMENT 'Basket id (oxuserbaskets)';
SQL;

        $this->addSql($sql);
    }

    public function down(Schema $schema): void
    {
        $sql = "ALTER TABLE `oxvouchers` DROP COLUMN `OXBASKETID`;";

        $this->addSql($sql);
    }
}
