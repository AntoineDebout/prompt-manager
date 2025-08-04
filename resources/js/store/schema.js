import Vuex from 'vuex'

export const store = new Vuex.Store({
  state: {
    schema: {
      type: "object",
      additionalProperties: false,
      properties: {
        comment: {
          type: "string",
          description: "Comment on the learner's answer based on their performance."
        },
        cecrl_level: {
          type: "string",
          enum: ["A1", "A2", "B1", "B2", "C1"],
          description: "Estimated CECRL level of the learner's answer based on their spoken transcript."
        }
      },
      required: ["cecrl_level", "comment"]
    },
    isValid: true
  },

  mutations: {
    UPDATE_SCHEMA(state, newSchema) {
      try {
        if (!newSchema) {
          return
        }

        // Si c'est une chaîne, la parser
        const parsed = typeof newSchema === 'string' ? JSON.parse(newSchema) : newSchema
        
        // Vérification approfondie
        if (typeof parsed !== 'object') {
          throw new Error('Schema invalide')
        }
        
        // Normaliser le format
        const normalized = JSON.parse(JSON.stringify(parsed))
        
        // Comparer avec l'état actuel
        const currentStr = JSON.stringify(state.schema)
        const newStr = JSON.stringify(normalized)
        
        if (currentStr !== newStr) {
          state.schema = normalized
        } 
      } catch (e) {
        console.error('Erreur lors de la mise à jour du schema:')
      }
    },
    SET_VALIDITY(state, isValid) {
      state.isValid = isValid
    }
  },

  actions: {
    updateSchema({ commit, state }, payload) {
      try {
        if (!payload) {
          return false
        }

        // Normaliser le payload
        const parsedSchema = typeof payload === 'string' 
          ? JSON.parse(payload) 
          : JSON.parse(JSON.stringify(payload))

        // Validation approfondie
        if (!parsedSchema || typeof parsedSchema !== 'object') {
          throw new Error('Le schema doit être un objet')
        }

        // Comparer avec l'état actuel
        const currentStr = JSON.stringify(state.schema)
        const newStr = JSON.stringify(parsedSchema)
        
        if (currentStr === newStr) {
          return true
        }
        
        commit('UPDATE_SCHEMA', parsedSchema)
        commit('SET_VALIDITY', true)
        return true
      } catch (e) {
        console.error('Erreur lors de la mise à jour du schema:')
        commit('SET_VALIDITY', false)
        return false
      }
    }
  },

  getters: {
    // Toujours retourner une chaîne formatée de manière cohérente
    formattedSchema: state => {
      try {
        return state.schema ? JSON.stringify(state.schema, null, 2) : '{}'
      } catch (e) {
        console.error('Erreur lors du formatage du schema')
        return '{}'
      }
    },
    isSchemaValid: state => state.isValid,
    // Assurer que le getter ne retourne jamais undefined
    rawSchema: state => state.schema || {
      type: "object",
      additionalProperties: false,
      properties: {},
      required: []
    }
  }
})
