export const variableLabels = {
  '{{site_language}}': 'Langue du site',
  '{{question_title}}': 'Titre de la question',
  '{{question_success_criteria}}': 'Critères de réussite',
  '{{question_suggestion}}': 'Suggestion de réponse',
  '{{question_language}}': 'Langue de la question',
  '{{question_support_material}}': 'Matériel de support',
  '{{user_input}}': 'Réponse de l\'utilisateur'
}

// Fonction utilitaire pour obtenir le label d'une variable
export const getVariableLabel = (variable) => {
  return variableLabels[variable] || variable
}

// Fonction utilitaire pour obtenir la clé d'une variable à partir de son label
export const getVariableKey = (label) => {
  for (const [key, value] of Object.entries(variableLabels)) {
    if (value === label) return key
  }
  return label
}
