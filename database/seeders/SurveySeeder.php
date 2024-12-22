<?php

namespace Database\Seeders;

use App\Models\Survey;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            SurveyResultSeeder::class,
            ControlCallResultSeeder::class,
        ]);

        $survey = Survey::create([
            'name' => 'Опросник для оператора'
        ]);

        ///
        /// Самочувствие
        ///
        $surveyChapter = $survey->surveyChapters()->create([
            'name' => '1. Самочувствие'
        ]);

        $surveyQuestion = $surveyChapter->questions()->create([
            'question' => 'Как Вы себя чувствуете на сегодняшний день?',
            'survey_id' => $survey->id
        ]);

        $surveyQuestion->answers()->create([
            'answer' => 'Отлично'
        ]);
        $surveyQuestion->answers()->create([
            'answer' => 'Хорошо'
        ]);
        $surveyQuestion->answers()->create([
            'answer' => 'Удовлетворительно'
        ]);
        $surveyQuestion->answers()->create([
            'answer' => 'Плохо',
            'has_send_smp' => true,
            'has_send_doctor' => true
        ]);

        ///
        /// Боль
        ///
        $briefQuestionChapter = $survey->surveyChapters()->create([
            'name' => '2. Боль'
        ]);

        $briefQuestion = $briefQuestionChapter->questions()->create([
            'question' => 'Бывает ли у Вас боль, тяжесть, давление в грудной клетке?',
            'survey_id' => $survey->id
        ]);

        $briefQuestion->answers()->create([
            'answer' => 'Нет',
            'has_disable_other_answer' => true
        ]);
        $briefQuestion->answers()->create([
            'answer' => 'Да',
            'has_attention' => true
        ]);

        $briefQuestion = $briefQuestionChapter->questions()->create([
            'question' => 'Боль возникает',
            'survey_id' => $survey->id
        ]);

        $briefQuestion->answers()->create([
            'answer' => 'При быстрой ходьбе, подъеме в гору',
            'has_send_doctor' => true
        ]);
        $briefQuestion->answers()->create([
            'answer' => 'В покое, положении лежа, сидя'
        ]);

        $briefQuestion = $briefQuestionChapter->questions()->create([
            'question' => 'Длительность боли',
            'survey_id' => $survey->id
        ]);

        $briefQuestion->answers()->create([
            'answer' => 'До 15 минут',
            'has_send_doctor' => true
        ]);
        $briefQuestion->answers()->create([
            'answer' => 'Несколько секунд, более 30 минут, часами'
        ]);

        $briefQuestion = $briefQuestionChapter->questions()->create([
            'question' => 'Что Вы делаете при появлении боли?',
            'survey_id' => $survey->id
        ]);

        $briefQuestion->answers()->create([
            'answer' => 'Останавливаюсь или принимаю нитроглицерин',
            'has_send_doctor' => true
        ]);
        $briefQuestion->answers()->create([
            'answer' => 'Продолжаю идти в обычном темпе'
        ]);

        $briefQuestion = $briefQuestionChapter->questions()->create([
            'question' => 'За последние дни боли стали чаще, интенсивнее? По интенсивности похожи как во время инфаркта миокарда?',
            'survey_id' => $survey->id
        ]);

        $briefQuestion->answers()->create([
            'answer' => 'Да',
            'has_send_smp' => true
        ]);
        $briefQuestion->answers()->create([
            'answer' => 'Нет'
        ]);

        ///
        /// Одышка
        ///
        $briefQuestionChapter = $survey->surveyChapters()->create([
            'name' => '3. Одышка'
        ]);

        $briefQuestion = $briefQuestionChapter->questions()->create([
            'question' => 'Беспокоит ли Вас одышка?',
            'survey_id' => $survey->id
        ]);

        $briefQuestion->answers()->create([
            'answer' => 'В покое',
            'has_attention' => true
        ]);
        $briefQuestion->answers()->create([
            'answer' => 'При ходьбе',
            'has_attention' => true
        ]);
        $briefQuestion->answers()->create([
            'answer' => 'Нет',
        ]);

        ///
        /// Отеки
        ///
        $briefQuestionChapter = $survey->surveyChapters()->create([
            'name' => '4. Отеки'
        ]);
        $briefQuestion = $briefQuestionChapter->questions()->create([
            'question' => 'Есть отеки на ногах?',
            'survey_id' => $survey->id
        ]);

        $briefQuestion->answers()->create([
            'answer' => 'Да',
            'has_attention' => true
        ]);
        $briefQuestion->answers()->create([
            'answer' => 'Нет',
        ]);

        ///
        /// Сердцебиение
        ///
        $briefQuestionChapter = $survey->surveyChapters()->create([
            'name' => '5. Сердцебиение'
        ]);
        $briefQuestion = $briefQuestionChapter->questions()->create([
            'question' => 'Учащенное сердцебиение, перебои в работе сердца, чувство замирания',
            'survey_id' => $survey->id
        ]);

        $briefQuestion->answers()->create([
            'answer' => 'Да',
            'has_need_consult_doctor' => true
        ]);
        $briefQuestion->answers()->create([
            'answer' => 'Нет',
        ]);

        ///
        /// Мониторинг АД/ЧСС
        ///
        $briefQuestionChapter = $survey->surveyChapters()->create([
            'name' => '6. Мониторинг АД/ЧСС'
        ]);

        $briefQuestion = $briefQuestionChapter->questions()->create([
            'question' => 'Контроль АД',
            'survey_id' => $survey->id
        ]);

        $briefQuestion->answers()->create([
            'answer' => '<90 мм рт. ст.',
            'has_send_smp' => true
        ]);
        $briefQuestion->answers()->create([
            'answer' => '100/70 мм рт. ст.',
            'has_need_consult_doctor' => true
        ]);
        $briefQuestion->answers()->create([
            'answer' => '110-140/80 мм рт. ст.',
        ]);
        $briefQuestion->answers()->create([
            'answer' => '>180/100 мм рт. ст.',
            'has_send_smp' => true
        ]);

        $briefQuestion = $briefQuestionChapter->questions()->create([
            'question' => 'Контроль ЧСС',
            'survey_id' => $survey->id
        ]);

        $briefQuestion->answers()->create([
            'answer' => '<40 ударов в минуту',
            'has_send_smp' => true
        ]);
        $briefQuestion->answers()->create([
            'answer' => '45-55 ударов в минуту',
            'has_need_consult_doctor' => true
        ]);
        $briefQuestion->answers()->create([
            'answer' => '55-70 ударов в минуту',
        ]);
        $briefQuestion->answers()->create([
            'answer' => '>80 ударов в минуту',
            'has_need_consult_doctor' => true
        ]);

        ///
        /// Выписка
        ///
        $briefQuestionChapter = $survey->surveyChapters()->create([
            'name' => '7. Посещение врача'
        ]);

        $briefQuestion = $briefQuestionChapter->questions()->create([
            'question' => 'После выписки из стационара Вас посетил участковый врач (Вы обратились на прием)?',
            'survey_id' => $survey->id
        ]);

        $briefQuestion->answers()->create([
            'answer' => 'Да',
        ]);
        $briefQuestion->answers()->create([
            'answer' => 'Нет',
            'has_send_doctor' => true
        ]);

        ///
        /// Медицинские препараты
        ///
        $briefQuestionChapter = $survey->surveyChapters()->create([
            'name' => '8. Медицинские препараты'
        ]);

        $briefQuestion = $briefQuestionChapter->questions()->create([
            'question' => 'Вы получили препараты, рекомендованные Вам кардиологом при выписке из стационара?',
            'survey_id' => $survey->id
        ]);

        $briefQuestion->answers()->create([
            'answer' => 'Выдали в стационаре при выписке',
        ]);
        $briefQuestion->answers()->create([
            'answer' => 'Выписали рецепты в поликлинике',
        ]);
        $briefQuestion->answers()->create([
            'answer' => 'Нет',
            'has_need_consult_doctor' => true,
//            'has_disable_other_answer' => true,
        ]);

        $briefQuestion = $briefQuestionChapter->questions()->create([
            'question' => 'Вы принимаете в настоящее время препараты, препятствующие тромбозу стентов? (Ацетилсалициловая кислота, Клопидогрел, Тикагрелор (Брилинта)',
            'survey_id' => $survey->id
        ]);

        $briefQuestion->answers()->create([
            'answer' => 'Да',
        ]);
        $briefQuestion->answers()->create([
            'answer' => 'Нет',
            'has_attention' => true
        ]);

        $briefQuestion = $briefQuestionChapter->questions()->create([
            'question' => 'Вы принимаете назначенные препараты в прежних дозировках?',
            'survey_id' => $survey->id
        ]);

        $briefQuestion->answers()->create([
            'answer' => 'Да',
        ]);
        $briefQuestion->answers()->create([
            'answer' => 'Нет',
            'has_attention' => true
        ]);

        $briefQuestion = $briefQuestionChapter->questions()->create([
            'question' => 'Была ли у Вас замена или отмена препаратов?',
            'survey_id' => $survey->id
        ]);

        $briefQuestion->answers()->create([
            'answer' => 'Да',
            'has_attention' => true,
            'enable_answer_ids' => serialize([41, 42, 43, 44])
        ]);
        $briefQuestion->answers()->create([
            'answer' => 'Нет',
            'has_disable_answers' => true,
            'disable_answer_ids' => serialize([41, 42, 43, 44])
        ]);


        $briefQuestion = $briefQuestionChapter->questions()->create([
            'question' => 'По чьей инициативе отменены или заменены препараты?',
            'survey_id' => $survey->id
        ]);

        $briefQuestion->answers()->create([
            'answer' => 'Врач',
        ]);
        $briefQuestion->answers()->create([
            'answer' => 'Самостоятельно',
            'has_attention' => true
        ]);

        $briefQuestion = $briefQuestionChapter->questions()->create([
            'question' => 'Как давно Вы отменили эти  препараты?',
            'survey_id' => $survey->id
        ]);

        $briefQuestion->answers()->create([
            'answer' => 'До 5 дней',
        ]);
        $briefQuestion->answers()->create([
            'answer' => 'Более 5 дней',
            'has_attention' => true
        ]);

        ///
        /// Следующий визит
        ///
        $briefQuestionChapter = $survey->surveyChapters()->create([
            'name' => '9. Следующий визит'
        ]);

        $briefQuestion = $briefQuestionChapter->questions()->create([
            'question' => 'Участковый терапевт/кардиолог сообщили Вам дату следующего визита?',
            'survey_id' => $survey->id
        ]);

        $briefQuestion->answers()->create([
            'answer' => 'Да',
        ]);
        $briefQuestion->answers()->create([
            'answer' => 'Нет',
            'has_attention' => true
        ]);
    }
}
