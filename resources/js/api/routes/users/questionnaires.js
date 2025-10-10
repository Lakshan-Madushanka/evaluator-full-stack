export const get_all_route = (userId) => `administrative/users/${userId}/questionnaires`

export const get_attach_route = (userId, questionnaireId) =>
  `administrative/users/${userId}/questionnaires/${questionnaireId}/attach`

export const get_resent_notification_route = (userId, questionnaireId) =>
  `administrative/users/${userId}/questionnaire/${questionnaireId}/resend-notification`

export const get_detach_route = (userId, userQuestionnaireId) =>
  `administrative/users/${userId}/questionnaires/${userQuestionnaireId}/detach`
