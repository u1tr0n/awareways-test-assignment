<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250928143748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create DB initial structure';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE categories (id UUID NOT NULL, title VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_category_title ON categories (title)');
        $this->addSql('COMMENT ON COLUMN categories.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN categories.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN categories.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE clients (id UUID NOT NULL, quiz_id UUID NOT NULL, name VARCHAR(255) NOT NULL, version VARCHAR(15) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C82E74853CD175 ON clients (quiz_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_client ON clients (name, version)');
        $this->addSql('COMMENT ON COLUMN clients.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN clients.quiz_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN clients.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN clients.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE clients_answers (id UUID NOT NULL, client_id UUID NOT NULL, question_id UUID NOT NULL, answer_id UUID NOT NULL, is_correct BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6EBB660919EB6921 ON clients_answers (client_id)');
        $this->addSql('CREATE INDEX IDX_6EBB66091E27F6BF ON clients_answers (question_id)');
        $this->addSql('CREATE INDEX IDX_6EBB6609AA334807 ON clients_answers (answer_id)');
        $this->addSql('COMMENT ON COLUMN clients_answers.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN clients_answers.client_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN clients_answers.question_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN clients_answers.answer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN clients_answers.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN clients_answers.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE options (id UUID NOT NULL, ref_id UUID DEFAULT NULL, title VARCHAR(255) NOT NULL, is_correct BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D035FA8721B741A9 ON options (ref_id)');
        $this->addSql('COMMENT ON COLUMN options.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN options.ref_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN options.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN options.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE questions (id UUID NOT NULL, category_id UUID DEFAULT NULL, ref_id UUID DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, type VARCHAR(20) NOT NULL, meta JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8ADC54D512469DE2 ON questions (category_id)');
        $this->addSql('CREATE INDEX IDX_8ADC54D521B741A9 ON questions (ref_id)');
        $this->addSql('COMMENT ON COLUMN questions.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN questions.category_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN questions.ref_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN questions.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN questions.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE question_tag (question_id UUID NOT NULL, tag_id UUID NOT NULL, PRIMARY KEY(question_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_339D56FB1E27F6BF ON question_tag (question_id)');
        $this->addSql('CREATE INDEX IDX_339D56FBBAD26311 ON question_tag (tag_id)');
        $this->addSql('COMMENT ON COLUMN question_tag.question_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN question_tag.tag_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE questions_options (id UUID NOT NULL, question_id UUID NOT NULL, option_id UUID NOT NULL, sort_position INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A3B0DA411E27F6BF ON questions_options (question_id)');
        $this->addSql('CREATE INDEX IDX_A3B0DA41A7C41D6F ON questions_options (option_id)');
        $this->addSql('COMMENT ON COLUMN questions_options.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN questions_options.question_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN questions_options.option_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN questions_options.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN questions_options.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE quizzes (id UUID NOT NULL, title VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_quiz_title ON quizzes (title)');
        $this->addSql('COMMENT ON COLUMN quizzes.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN quizzes.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN quizzes.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE quizzes_questions (id UUID NOT NULL, question_id UUID NOT NULL, quiz_version_id UUID NOT NULL, sort_position INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F08609261E27F6BF ON quizzes_questions (question_id)');
        $this->addSql('CREATE INDEX IDX_F08609262D30039 ON quizzes_questions (quiz_version_id)');
        $this->addSql('COMMENT ON COLUMN quizzes_questions.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN quizzes_questions.question_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN quizzes_questions.quiz_version_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN quizzes_questions.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN quizzes_questions.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE quizzes_versions (id UUID NOT NULL, quiz_id UUID NOT NULL, ref_id UUID DEFAULT NULL, version VARCHAR(20) NOT NULL, status VARCHAR(20) DEFAULT \'draft\' NOT NULL, current_bitmask INT DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_29DB1B59853CD175 ON quizzes_versions (quiz_id)');
        $this->addSql('CREATE INDEX IDX_29DB1B5921B741A9 ON quizzes_versions (ref_id)');
        $this->addSql('CREATE INDEX idx_quiz_versions_quiz_version_status ON quizzes_versions (version, status)');
        $this->addSql('CREATE INDEX idx_quiz_versions_quiz_status ON quizzes_versions (status)');
        $this->addSql('CREATE UNIQUE INDEX uniq_quiz_version_per_quiz ON quizzes_versions (quiz_id, version)');
        $this->addSql('COMMENT ON COLUMN quizzes_versions.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN quizzes_versions.quiz_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN quizzes_versions.ref_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN quizzes_versions.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN quizzes_versions.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE tags (id UUID NOT NULL, title VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_tag_title ON tags (title)');
        $this->addSql('COMMENT ON COLUMN tags.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN tags.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN tags.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE clients ADD CONSTRAINT FK_C82E74853CD175 FOREIGN KEY (quiz_id) REFERENCES quizzes_versions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE clients_answers ADD CONSTRAINT FK_6EBB660919EB6921 FOREIGN KEY (client_id) REFERENCES clients (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE clients_answers ADD CONSTRAINT FK_6EBB66091E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE clients_answers ADD CONSTRAINT FK_6EBB6609AA334807 FOREIGN KEY (answer_id) REFERENCES options (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE options ADD CONSTRAINT FK_D035FA8721B741A9 FOREIGN KEY (ref_id) REFERENCES options (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D512469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D521B741A9 FOREIGN KEY (ref_id) REFERENCES questions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE question_tag ADD CONSTRAINT FK_339D56FB1E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE question_tag ADD CONSTRAINT FK_339D56FBBAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE questions_options ADD CONSTRAINT FK_A3B0DA411E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE questions_options ADD CONSTRAINT FK_A3B0DA41A7C41D6F FOREIGN KEY (option_id) REFERENCES options (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quizzes_questions ADD CONSTRAINT FK_F08609261E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quizzes_questions ADD CONSTRAINT FK_F08609262D30039 FOREIGN KEY (quiz_version_id) REFERENCES quizzes_versions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quizzes_versions ADD CONSTRAINT FK_29DB1B59853CD175 FOREIGN KEY (quiz_id) REFERENCES quizzes (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quizzes_versions ADD CONSTRAINT FK_29DB1B5921B741A9 FOREIGN KEY (ref_id) REFERENCES quizzes_versions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE clients DROP CONSTRAINT FK_C82E74853CD175');
        $this->addSql('ALTER TABLE clients_answers DROP CONSTRAINT FK_6EBB660919EB6921');
        $this->addSql('ALTER TABLE clients_answers DROP CONSTRAINT FK_6EBB66091E27F6BF');
        $this->addSql('ALTER TABLE clients_answers DROP CONSTRAINT FK_6EBB6609AA334807');
        $this->addSql('ALTER TABLE options DROP CONSTRAINT FK_D035FA8721B741A9');
        $this->addSql('ALTER TABLE questions DROP CONSTRAINT FK_8ADC54D512469DE2');
        $this->addSql('ALTER TABLE questions DROP CONSTRAINT FK_8ADC54D521B741A9');
        $this->addSql('ALTER TABLE question_tag DROP CONSTRAINT FK_339D56FB1E27F6BF');
        $this->addSql('ALTER TABLE question_tag DROP CONSTRAINT FK_339D56FBBAD26311');
        $this->addSql('ALTER TABLE questions_options DROP CONSTRAINT FK_A3B0DA411E27F6BF');
        $this->addSql('ALTER TABLE questions_options DROP CONSTRAINT FK_A3B0DA41A7C41D6F');
        $this->addSql('ALTER TABLE quizzes_questions DROP CONSTRAINT FK_F08609261E27F6BF');
        $this->addSql('ALTER TABLE quizzes_questions DROP CONSTRAINT FK_F08609262D30039');
        $this->addSql('ALTER TABLE quizzes_versions DROP CONSTRAINT FK_29DB1B59853CD175');
        $this->addSql('ALTER TABLE quizzes_versions DROP CONSTRAINT FK_29DB1B5921B741A9');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE clients');
        $this->addSql('DROP TABLE clients_answers');
        $this->addSql('DROP TABLE options');
        $this->addSql('DROP TABLE questions');
        $this->addSql('DROP TABLE question_tag');
        $this->addSql('DROP TABLE questions_options');
        $this->addSql('DROP TABLE quizzes');
        $this->addSql('DROP TABLE quizzes_questions');
        $this->addSql('DROP TABLE quizzes_versions');
        $this->addSql('DROP TABLE tags');
    }
}
