export const get_all_route = (id) => `administrative/questionnaires/${id}/questions`

export const get_route_to_check_question_eligibility = (questionnaireId, questionId) =>
  `administrative/questionnaires/${questionnaireId}/eligible/${questionId}`

export const get_all_eligible_questions = (questionnaireId) =>
  `administrative/questionnaires/${questionnaireId}/eligible-questions`

export const get_sync_questions_route = (questionnaireId) =>
  `administrative/questionnaires/${questionnaireId}/questions`
