# script de.py
#(C) Fabrice Sincère

from tkinter import *
import random

def NouveauLance():
    nb = random.randint(1,6)
    Texte.set('Résultat -> ' + str(nb))

# Création de la fenêtre principale (main window)
Mafenetre = Tk()

Mafenetre.title('Dé à 6 faces')
Mafenetre.geometry('300x100+400+400')

# Création d'un widget Button (bouton Lancer)
BoutonLancer = Button(Mafenetre, text ='Lancer', command = NouveauLance)
# Positionnement du widget avec la méthode pack()
BoutonLancer.pack(side = LEFT, padx = 5, pady = 5)

# Création d'un widget Button (bouton Quitter)
BoutonQuitter = Button(Mafenetre, text ='Quitter', command = Mafenetre.destroy)
BoutonQuitter.pack(side = LEFT, padx = 5, pady = 5)

Texte = StringVar()
NouveauLance()

# Création d'un widget Label (texte 'Résultat -> x')
LabelResultat = Label(Mafenetre, textvariable = Texte, fg ='red', bg ='white')
LabelResultat.pack(side = LEFT, padx = 5, pady = 5)

Mafenetre.mainloop()
