import * as questionsRoutes from '@/api/routes/questionnaires/questions'
import http from '@/http'
import { buildQueryString } from '@/api/queryStringBuilder'

export async function getAllRequest(id, query) {
  query = buildQueryString(query)
  const response = await http.get(questionsRoutes.get_all_route(id) + query)

  return response
}

export async function getAllEligibleQuestionsRequest(id, query) {
  query = buildQueryString(query)
  const response = await http.get(questionsRoutes.get_all_eligible_questions(id) + query)

  return response
}

export async function checkQuestionEligibilityRequest(questionnaireId, questionId) {
  const response = await http.get(
    questionsRoutes.get_route_to_check_question_eligibility(questionnaireId, questionId)
  )

  return response
}

export async function syncQuestionsRequest(questionnaireId, questions) {
  const response = await http.post(
    questionsRoutes.get_sync_questions_route(questionnaireId),
    questions
  )

  return response
}
