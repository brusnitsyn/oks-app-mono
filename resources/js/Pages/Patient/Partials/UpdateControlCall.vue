<script setup>
import {inject, ref, watch} from "vue";
import {
    NCollapse,
    NCollapseItem,
    NFormItemGi, NGi,
    NGrid,
    NGridItem,
    NRadio,
    NRadioGroup,
    NSelect,
    NSpace,
    NText
} from "naive-ui";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    controlCall: {
        type: Object
    }
})

const form = useForm({
    control_call: {},
    answers: []
})

const { updateTitle, updateShow } = inject('modal')
const { callResults, surveyResults } = inject('patient')
updateTitle(props.controlCall.name)
const shadowSelectedAnswers = ref([])

function hasDisableAnswer(answerId, questionId) {
    const lastAnswer = shadowSelectedAnswers.value.find(itm => itm.id === answerId)
    const containDisableAnswers = shadowSelectedAnswers.value.filter(itm => itm.id === lastAnswer.id)
    const containDisableQuestionIndex = containDisableAnswers.findIndex(itm => itm.has_disable_other_answer === true)
    const containDisableAnswersIndex = containDisableAnswers.findIndex(itm => itm.has_disable_answers === true)

    if (containDisableQuestionIndex !== -1) {
        const disableOtherAnswers = shadowSelectedAnswers.value.filter(itm => itm.has_disable_other_answer === true)
        for (const disableOtherAnswer of disableOtherAnswers) {
            const chapter = props.controlCall.survey.survey_chapters.find(itm => {
                const find = itm.questions.find(q => q.id === lastAnswer.survey_chapter_question_id)
                if (typeof find != 'undefined') return itm.id === find.survey_chapter_id
            })
            const questionsToDisable = chapter.questions.filter(itm => !(itm.id === disableOtherAnswer.survey_chapter_question_id))

            console.log(questionsToDisable)

            for (const question of questionsToDisable) {
                const spliceIndex = shadowSelectedAnswers.value.findIndex(itm => itm.survey_chapter_question_id === question.id - 1)
                if (spliceIndex !== -1) {
                    shadowSelectedAnswers.value.splice(spliceIndex, 1)
                }
                if (disableOtherAnswer.has_disable_other_answer) {
                    question.disabled = true
                    form.answers[question.id] = null
                }
            }
        }
    }
    else {
        const chapter = props.controlCall.survey.survey_chapters.find(itm => {
            const find = itm.questions.find(q => q.id === lastAnswer.survey_chapter_question_id)
            if (typeof find != 'undefined') return itm.id === find.survey_chapter_id
        })
        if (typeof chapter !== 'undefined') {
            const questionsToDisabled = chapter.questions.filter(itm => itm.disabled === true)
            for (const question of questionsToDisabled) {
                question.disabled = false
            }
        }
    }
    console.log(containDisableAnswersIndex)
    if (containDisableAnswersIndex !== -1) {
        const disableAnswers = shadowSelectedAnswers.value.filter(itm => itm.has_disable_answers === true)
        console.log(disableAnswers)
        for (const disableAnswer of disableAnswers) {
            const chapter = props.controlCall.survey.survey_chapters.find(itm => {
                const find = itm.questions.find(q => q.id === lastAnswer.survey_chapter_question_id)
                if (typeof find != 'undefined') return itm.id === find.survey_chapter_id
            })
            const questionsToDisable = chapter.questions.filter(itm => !(itm.id === disableAnswer.survey_question_chapter_id))

            let disabledTo = []

            for (const question of questionsToDisable) {
                const spliceIndex = shadowSelectedAnswers.value.findIndex(itm => itm.survey_question_chapter_id === question.id - 1)
                if (spliceIndex !== -1) {
                    shadowSelectedAnswers.value.splice(spliceIndex, 1)
                }
                if (disableAnswer.has_disable_answers) {
                    disabledTo = disableAnswer.disable_answer_ids
                    for (const ans of question.answers) {
                        if (disabledTo.length > 0 && disabledTo.includes(ans.id)) {
                            ans.disabled = true
                            form.answers[question.id] = null
                        }
                    }
                }
            }
        }
    }
    else {
        const chapter = props.controlCall.survey.survey_chapters.find(itm => {
            const find = itm.questions.find(q => q.id === lastAnswer.survey_chapter_question_id)
            if (typeof find != 'undefined') return itm.id === find.survey_chapter_id
        })
        const questionsToDisable = chapter.questions.filter(itm => !(itm.id === lastAnswer.survey_question_chapter_id))

        let enabledTo = []

        for (const question of questionsToDisable) {
            console.log(lastAnswer.enable_answer_ids)
            if (lastAnswer.enable_answer_ids) {
                enabledTo = lastAnswer.enable_answer_ids

                for (const ans of question.answers) {
                    if (enabledTo.length > 0 && enabledTo.includes(ans.id)) {
                        ans.disabled = false
                    }
                }
            }
        }
    }
}

for (const briefAnswer of Object.values(props.controlCall.survey_answers)) {
    const findShadow = form.answers.find(itm => itm.id === briefAnswer)
    shadowSelectedAnswers.value.push(findShadow)
    hasDisableAnswer(findShadow.id)
}

function onCheckSurveyAnswer(chapterId, answerId, question) {
    const findShadow = props.controlCall.survey.survey_answers.find(itm => itm.id === answerId)
    const duplicationIndex = shadowSelectedAnswers.value.findIndex(itm => itm.survey_chapter_question_id === question.id)

    if (duplicationIndex !== -1) {
        shadowSelectedAnswers.value.splice(duplicationIndex, 1)
    }

    shadowSelectedAnswers.value.push(findShadow)

    hasDisableAnswer(answerId, question.id)
}

watch(shadowSelectedAnswers.value, (newValue) => {
    console.log(newValue)
    const hasSendSmp = newValue.findIndex(itm => itm.has_send_smp === true)
    if (hasSendSmp !== -1) {
        form.control_call.survey_result_id = 6
        return
    }

    const hasSendDoctor = newValue.findIndex(itm => itm.has_send_doctor === true)
    if (hasSendDoctor !== -1) {
        form.control_call.survey_result_id = 5
        return
    }

    const hasAttention = newValue.findIndex(itm => itm.has_attention === true)
    if (hasAttention !== -1) {
        form.control_call.survey_result_id = 4
    }

    if (hasSendSmp === -1 && hasSendDoctor === -1 && hasAttention === -1) {
        form.control_call.survey_result_id = 2
    }
})
</script>

<template>
    <NForm>
        <NGrid cols="2" x-gap="6">
            <NFormItemGi label="Результат дозвона">
                <NSelect placeholder="" :options="callResults" label-field="name" value-field="id" />
            </NFormItemGi>
            <NFormItemGi label="Результат опроса">
                <NSelect disabled v-model:value="form.control_call.survey_result_id" :options="surveyResults" placeholder="" label-field="name" value-field="id" />
            </NFormItemGi>

            <NGi span="2">
                <NCollapse accordion>
                    <template v-for="(chapter, index) in controlCall.survey.survey_chapters">
                        <NCollapseItem :title="chapter.name" :name="index" :disabled="controlCall.result_call_id === null || form.control_call.control_point_option_id === 1">
                            <NGrid cols="1" x-gap="8" y-gap="8" class="px-6">
                                <template v-for="(question, index) in chapter.questions">
                                    <NGridItem>
                                        <NSpace vertical>
                                            <NText class="font-medium">
                                                {{ index + 1 }}. {{ question.question }}
                                            </NText>
                                            <NRadioGroup v-model:value="form.answers[question.id]" :disabled="controlCall.called_at !== null || question.disabled" :name="question.question" @update:value="answerId => onCheckSurveyAnswer(chapter.id, answerId, question)">
                                                <NRadio v-for="answer in question.answers" :label="answer.answer" :disabled="answer.disabled" :value="answer.id" />
                                            </NRadioGroup>
                                        </NSpace>
                                    </NGridItem>
                                </template>
                            </NGrid>
                        </NCollapseItem>
                    </template>
                </NCollapse>
            </NGi>


        </NGrid>
    </NForm>
</template>

<style scoped>

</style>
