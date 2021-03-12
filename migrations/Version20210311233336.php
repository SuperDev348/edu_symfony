<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210311233336 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(100) NOT NULL, adult_number INT NOT NULL, time VARCHAR(255) NOT NULL, date DATE NOT NULL, listing_id INT NOT NULL, children_number INT NOT NULL, phone_number VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE listing (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, price_range VARCHAR(20) NOT NULL, description TEXT NOT NULL, status VARCHAR(255) NOT NULL, feature TINYINT(1) NOT NULL, category VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, place_type VARCHAR(100) NOT NULL, wifi TINYINT(1) NOT NULL, parking TINYINT(1) NOT NULL, accept_card TINYINT(1) NOT NULL, garden TINYINT(1) NOT NULL, airport_taxi TINYINT(1) NOT NULL, terrace TINYINT(1) NOT NULL, toilet TINYINT(1) NOT NULL, air_conditioner TINYINT(1) NOT NULL, address VARCHAR(100) NOT NULL, la_g NUMERIC(16, 13) NOT NULL, la_i NUMERIC(16, 13) NOT NULL, ra_g NUMERIC(16, 13) NOT NULL, ra_i NUMERIC(16, 13) NOT NULL, lat NUMERIC(16, 13) NOT NULL, lng NUMERIC(16, 13) NOT NULL, googlemap_address VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, phone VARCHAR(100) DEFAULT NULL, website VARCHAR(100) DEFAULT NULL, facebook_url VARCHAR(100) DEFAULT NULL, instagram_url VARCHAR(100) DEFAULT NULL, youtube_url VARCHAR(100) DEFAULT NULL, twitter_url VARCHAR(100) DEFAULT NULL, google_url VARCHAR(100) DEFAULT NULL, pinterest_url VARCHAR(100) DEFAULT NULL, snapchat_url VARCHAR(100) DEFAULT NULL, facebook VARCHAR(100) DEFAULT NULL, instagram TINYINT(1) NOT NULL, youtube TINYINT(1) NOT NULL, twitter TINYINT(1) NOT NULL, google TINYINT(1) NOT NULL, pinterest TINYINT(1) NOT NULL, snapchat TINYINT(1) NOT NULL, monday TINYINT(1) NOT NULL, tuesday TINYINT(1) NOT NULL, wednesday TINYINT(1) NOT NULL, thursday TINYINT(1) NOT NULL, friday TINYINT(1) NOT NULL, saturday TINYINT(1) NOT NULL, sunday TINYINT(1) NOT NULL, monday_start_time INT DEFAULT NULL, monday_end_time INT DEFAULT NULL, tuesday_start_time INT DEFAULT NULL, tuesday_end_time INT DEFAULT NULL, wednesday_start_time INT DEFAULT NULL, wednesday_end_time INT DEFAULT NULL, thursday_start_time INT DEFAULT NULL, thursday_end_time INT DEFAULT NULL, friday_start_time INT DEFAULT NULL, friday_end_time INT DEFAULT NULL, saturday_start_time INT DEFAULT NULL, saturday_end_time INT DEFAULT NULL, sunday_start_time INT DEFAULT NULL, sunday_end_time INT DEFAULT NULL, cover_image VARCHAR(100) NOT NULL, gallery_image VARCHAR(100) DEFAULT NULL, video VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, user_name VARCHAR(100) NOT NULL, user_avatar VARCHAR(100) NOT NULL, listing_id INT NOT NULL, rate INT NOT NULL, date DATE NOT NULL, feature TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE setting (id INT AUTO_INCREMENT NOT NULL, visit_number INT DEFAULT NULL, booking_block TINYINT(1) DEFAULT NULL, booking_block_from DATE DEFAULT NULL, booking_block_to DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE suggestion (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE listing');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE setting');
        $this->addSql('DROP TABLE suggestion');
    }
}
