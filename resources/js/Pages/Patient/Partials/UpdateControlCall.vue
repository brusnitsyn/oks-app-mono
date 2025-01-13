<script setup>
import {computed, inject, ref, watch} from "vue";
import {
    NButton,
    NCollapse,
    NCollapseItem, NFlex, NForm,
    NFormItemGi, NGi,
    NGrid,
    NGridItem, NIcon, NInput,
    NRadio,
    NRadioGroup,
    NSelect,
    NSpace,
    NText
} from "naive-ui";
import {router, useForm, usePage} from "@inertiajs/vue3";
import {IconArrowLeft, IconArrowRight, IconCancel, IconCheck} from "@tabler/icons-vue";
import AppDatePicker from "@/Components/AppDatePicker.vue";

const props = defineProps({
    controlCall: {
        type: Object
    }
})

// const controlCall = defineModel('controlСall')

const page = usePage()

const propsData = computed(() => props.controlCall)
const formRef = ref()
const form = useForm({
    med_card_control_call_id: propsData.value.id,
    info:  propsData.value.info,
    answers: new Map(Object.entries(propsData.value.answers)),
    survey_result_id:  propsData.value.survey_result_id,
    control_call_result_id:  propsData.value.control_call_result_id,
    survey_id:  propsData.value.survey_id,
    disp_start_at: null,
    completed_chapters: []
})

const { updateTitle, updateShow } = inject('modal')
const callResults = ref(page.props.callResults)
const surveyResults = ref(page.props.surveyResults)
updateTitle(propsData.value.name)
const shadowSelectedAnswers = ref([])
const hasShowDispPicker = ref(false)
const hasShowDisp = computed({
    get() {
        return hasShowDispPicker.value
    },
    set(value) {
        hasShowDispPicker.value = value
        if (value === false)
            form.disp_start_at = null
    }
})

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
                if (typeof find !== 'undefined') return itm.id === find.survey_chapter_id
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
                    form.answers.delete(`${question.id}`)
                }
            }
        }
    }
    else {
        const chapter = props.controlCall.survey.survey_chapters.find(itm => {
            const find = itm.questions.find(q => q.id === lastAnswer.survey_chapter_question_id)
            if (typeof find !== 'undefined') return itm.id === find.survey_chapter_id
        })
        if (typeof chapter !== 'undefined') {
            const questionsToDisabled = chapter.questions.filter(itm => itm.disabled === true && itm.has_disable_other_answer !== true)
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
                if (typeof find !== 'undefined') return itm.id === find.survey_chapter_id
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
                            question.disabled = true
                            ans.disabled = true
                            form.answers.delete(`${question.id}`)
                            // form.answers[question.id] = null
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
                        question.disabled = false
                        ans.disabled = false
                    }
                }
            }
        }
    }
}

// Предварительная проверка ответов
for (const answerId of Object.values(propsData.value.answers)) {
    const findShadow = propsData.value.survey.survey_answers.find(itm => itm.id === answerId)
    shadowSelectedAnswers.value.push(findShadow)
    hasDisableAnswer(findShadow.id)
}

// Предварительная калькуляция процентов ответов
for (const surveyChapter of propsData.value.survey.survey_chapters) {
    calculateAnswerPercent(surveyChapter.id)
}

function onCheckSurveyAnswer(chapterId, answerId, question) {
    form.answers.set(`${question.id}`, answerId)

    const findShadow = propsData.value.survey.survey_answers.find(itm => itm.id === answerId)
    const duplicationIndex = shadowSelectedAnswers.value.findIndex(itm => itm.survey_chapter_question_id === question.id)

    if (findShadow.has_show_disp_date_picker === true) {
        hasShowDisp.value = true
    } else if (findShadow.has_show_disp_date_picker === false) {
        hasShowDisp.value = false
    }

    if (duplicationIndex !== -1) {
        shadowSelectedAnswers.value.splice(duplicationIndex, 1)
    }

    shadowSelectedAnswers.value.push(findShadow)

    hasDisableAnswer(answerId, question.id)

    calculateAnswerPercent(chapterId)
}

// Процент ответов в главе
function calculateAnswerPercent(chapterId) {
    // Вопросы в главе
    const allQuestionsInChapter = propsData.value.survey.survey_chapters.find(chapter => chapter.id === chapterId)
        .questions.filter(function (quest) {
            return (quest.disabled === false || typeof quest.disabled === 'undefined')
        })
    // Кол-во ответов
    let countAnswer = 0
    for (const questionInChapter of allQuestionsInChapter) {
        // console.log(questionInChapter)
        const hasAnswer = form.answers.get(`${questionInChapter.id}`)
        if (hasAnswer !== null && typeof hasAnswer !== 'undefined') {
            console.log(hasAnswer)
            countAnswer++
        }
    }
    // Считаем процент
    const percent = countAnswer * 100 / allQuestionsInChapter.length
    // console.log(`${percent}%`)
    // console.log(countAnswer)
    console.log(allQuestionsInChapter)
    form.completed_chapters[chapterId] = percent
}

watch(shadowSelectedAnswers.value, (newValue) => {
    // console.log(newValue)
    const hasSendSmp = newValue.findIndex(itm => itm.has_send_smp === true)
    if (hasSendSmp !== -1) {
        form.survey_result_id = 6
        return
    }

    const hasSendDoctor = newValue.findIndex(itm => itm.has_send_doctor === true)
    if (hasSendDoctor !== -1) {
        form.survey_result_id = 5
        return
    }

    const hasAttention = newValue.findIndex(itm => itm.has_attention === true)
    if (hasAttention !== -1) {
        form.survey_result_id = 4
    }

    if (hasSendSmp === -1 && hasSendDoctor === -1 && hasAttention === -1) {
        form.survey_result_id = 2
    }
})

const messageDefault = 'Это поле обязательно!'

const rules = {
    'control_call_result_id': [
        {
            type: 'number',
            required: true,
            message: messageDefault,
            trigger: ['blur', 'change']
        }
    ],
    'survey_result_id': [
        {
            type: 'number',
            required: true,
            message: 'Пока чек-лист пуст - результата не будет',
            trigger: ['blur', 'change']
        }
    ],
}

const resultCall = computed({
    get() {
        return form.control_call_result_id
    },
    set(value) {
        switch (value) {
            case 1:
                form.survey_result_id = null
                break
            case 2:
                form.survey_result_id = 1
                form.answers = {}
                break
            case 3:
                form.survey_result_id = 1
                break
        }

        form.control_call_result_id = value
    }
})

function onCloseClick() {
    updateShow(false)
}

function onSuccessClick() {
    formRef.value?.validate((errors) => {
        if (!errors) {
            form.transform((data) => ({
                ...data,
                answers: Object.fromEntries(data.answers)
            })).put(route('control.call.update', { controlCall: propsData.value.id }), {
                onSuccess: () => {
                    window.$message.success('Контрольная точка обновлена')
                    router.reload()
                    updateShow(false)
                },
                onError: (error) => {
                    window.$message.error('Ошибка при обновлении контрольной точки')
                    console.log(error)
                }
            })
        }
        else {
            window.$message.error('Проверьте заполнение чек-листа')
            console.log(errors)
        }
    })
}
const surveyExpanded = ref([])
const hasDisabledSurvey = computed(() => {
    if (form.control_call_result_id === null || form.control_call_result_id !== 1) {
        surveyExpanded.value = []
        return true
    }
    return false
})

function getAnswerValue(questionId) {
    return form.answers.get(`${questionId}`)
}
</script>

<template>
    <NForm :model="form" :rules="rules" ref="formRef">
        <NGrid cols="2" x-gap="6" y-gap="6">
            <NFormItemGi label="Результат дозвона" path="control_call_result_id">
                <NSelect placeholder="" :options="callResults" v-model:value="resultCall" label-field="name" value-field="id" />
            </NFormItemGi>
            <NFormItemGi label="Результат опроса" path="survey_result_id">
                <NSelect disabled v-model:value="form.survey_result_id" :options="surveyResults" placeholder="" label-field="name" value-field="id" />
            </NFormItemGi>

            <NGi span="2">
                <NCollapse accordion v-model:expanded-names="surveyExpanded">
                    <template v-for="(chapter, index) in propsData.survey.survey_chapters">
                        <NFormItem :path="`completed_chapters[${chapter.id}]`" :show-label="false" class="gap-y-3 relative" feedback-class="absolute w-auto right-0 top-1" :rule="{
                                        type: 'number',
                                        validator(rule, value) {
                                            if (value !== 100 && form.control_call_result_id === 1) {
                                                return new Error('Проверьте заполнение')
                                            }
                                        },
                                        trigger: ['input', 'change', 'blur'],
                                      }">
                            <NCollapseItem :title="chapter.name" :name="index" :disabled="hasDisabledSurvey">
                                <NGrid cols="1" x-gap="8" y-gap="8" class="px-6">
                                    <template v-for="(question, index) in chapter.questions">
                                        <NFormItemGi :show-feedback="false">
                                            <template #label>
                                                <NText class="font-medium">
                                                    {{ index + 1 }}. {{ question.question }}
                                                </NText>
                                            </template>
                                            <NRadioGroup :value="getAnswerValue(question.id)" :disabled=" question.disabled" :name="question.question" @update:value="answerId => onCheckSurveyAnswer(chapter.id, answerId, question)">
                                                <NRadio v-for="answer in question.answers" :label="answer.answer" :disabled="answer.disabled" :value="answer.id" />
                                            </NRadioGroup>
                                        </NFormItemGi>
                                    </template>
                                </NGrid>
                            </NCollapseItem>
                        </NFormItem>

                    </template>
                </NCollapse>
            </NGi>

            <NFormItemGi v-if="hasShowDispPicker" label="Диспансерный учёт" class="mt-4">
                <AppDatePicker v-model:value="form.disp_start_at" />
            </NFormItemGi>

            <NFormItemGi span="2" label="Комментарий" :class="hasShowDispPicker ? 'mt-0' : 'mt-4'">
                <NInput v-model:value="form.info" type="textarea" placeholder="" :autosize="{
                    minRows: 3,
                    maxRows: 3,
                }" />
            </NFormItemGi>

            <NGi span="2" class="mt-4">
                <NFlex align="center" justify="space-between">
                    <NButton secondary @click="onCloseClick">
                        <template #icon>
                            <NIcon :component="IconCancel" />
                        </template>
                        Отмена
                    </NButton>
                    <NSpace align="center" size="medium">
                        <NButton type="primary" icon-placement="right" @click="onSuccessClick">
                            <template #icon>
                                <NIcon :component="IconCheck" />
                            </template>
                            Завершить опрос
                        </NButton>
                    </NSpace>
                </NFlex>
            </NGi>
        </NGrid>
    </NForm>
</template>

<style scoped>
:deep(.n-form-item .n-form-item-feedback-wrapper) {
    grid-area: inherit;
}
</style>
