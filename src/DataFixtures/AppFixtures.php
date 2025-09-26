<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Option;
use App\Entity\Question;
use App\Entity\QuestionOption;
use App\Entity\Quiz;
use App\Entity\QuizQuestion;
use App\Entity\QuizVersion;
use App\Entity\Tag;
use App\Enum\QuestionType;
use App\Enum\QuizStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\UuidV7;

class AppFixtures extends Fixture
{
    /**
     * @throws \JsonException
     */
    public function load(ObjectManager $manager): void
    {
        $rawData = file_get_contents(__DIR__ . '/data.json', true);

        if (false === $rawData) {
            throw new \RuntimeException('Unable to read data.json');
        }

        /** @var array{
         *     categories: list<array{
         *         id: string,
         *         title: string,
         *     }>,
         *     tags: list<array{
         *         id: string,
         *         title: string,
         *     }>,
         *     quizzes: list<array{
         *         title: string,
         *         status: string,
         *         version: string,
         *         questions: list<array{
         *             title: string,
         *             description: string,
         *             type: string,
         *             meta: array<array-key, non-empty-string>,
         *             options: list<array{
         *                 title: string,
         *                 correct: bool,
         *             }>
         *         }>
         *     }>
         * } $data
         */
        $data = json_decode($rawData, true, 512, JSON_THROW_ON_ERROR);

        foreach ($data['tags'] as $tagData) {
            $tag = new Tag(new UuidV7($tagData['id']))->setTitle($tagData['title']);
            $manager->persist($tag);
        }

        foreach ($data['categories'] as $categoryData) {
            $category = new Category(new UuidV7($categoryData['id']))->setTitle($categoryData['title']);
            $manager->persist($category);
        }

        foreach ($data['quizzes'] as $quizData) {
            $quiz = new Quiz()->setTitle($quizData['title']);
            $manager->persist($quiz);

            $quizVersion = new QuizVersion()
                ->setQuiz($quiz)
                ->setVersion($quizData['version'])
                ->setStatus(QuizStatus::from($quizData['status']))
            ;
            $manager->persist($quizVersion);

            $questionPosition = 0;

            foreach ($quizData['questions'] as $questionData) {
                $question = new Question()
                    ->setTitle($questionData['title'])
                    ->setDescription($questionData['description'])
                    ->setType(QuestionType::from($questionData['type']))
                    ->setMeta($questionData['meta'])
                ;

                $manager->persist($question);

                $quizQuestion = new QuizQuestion()->setQuestion($question)->setQuizVersion($quizVersion)->setSortPosition(++$questionPosition);

                $manager->persist($quizQuestion);

                $optionPosition = 0;

                foreach ($questionData['options'] as $optionData) {
                    $option = new Option()->setTitle($optionData['title'])->setCorrect($optionData['correct']);
                    $manager->persist($option);

                    $questionOption = new QuestionOption()->setOption($option)->setQuestion($question)->setSortPosition(++$optionPosition);
                    $manager->persist($questionOption);
                }
            }
        }

        $manager->flush();
    }
}
