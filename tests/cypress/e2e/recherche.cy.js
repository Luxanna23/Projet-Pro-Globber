describe('Recherche sur la page d’accueil', () => {
  it('remplit le champ destination et lance la recherche', () => {
    cy.visit('/')
    cy.get('input[placeholder="Où allez-vous ?"]').type('Paris')
    cy.get('button').contains('Rechercher').click()
    cy.url().should('include', '/annonces')
  })
})