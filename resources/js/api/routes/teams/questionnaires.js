export const get_all_route = (teamId) => `administrative/teams/${teamId}/questionnaires`

export const get_all_users_route = (teamQuestionnaireId) =>
  `administrative/teams/questionnaires/${teamQuestionnaireId}/users`

export const get_attach_route = (teamId, questionnaireId) =>
  `administrative/teams/${teamId}/questionnaires/${questionnaireId}/attach`

export const get_detach_route = (teamId, questionnaireId) =>
  `administrative/teams/${teamId}/questionnaires/${questionnaireId}/detach`
