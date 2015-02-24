#!/usr/local/bin/python3
import random
import sys
import getopt
import re
import string

from tkinter import *

from optparse import OptionParser

#import curses
KEY_ESC = 27

h = 8
b = 2
g = 4
d = 6
score = 0
size_sprite = 24

options = OptionParser(usage='%prog server [options]', description='Test for SSL heartbeat vulnerability (CVE-2014-0160)')
options.add_option('-p', '--port', type='int', default=443, help='TCP port to test (default: 443)')

def init_curses(lignes, cols, pos):
	curses.initscr()
	curses.noecho() # pas de retour de ce qui est tapé
	curses.cbreak() # Pas besoin de tapez return
	curses.curs_set(0) # Pas aff curseur

	window = curses.newwin(lignes,cols,pos[0],pos[1])
	window.border(0)
	window.keypad(1)
	return window

def close_curses():
	curses.echo() # retour de ce qui est tapé
	curses.nocbreak() # Besoin de tapez return
	curses.curs_set(1) #  aff curseur
	curses.endwin()

def init_colors():
	curses.start_color()
	curses.init_pair(1, curses.COLOR_RED, curses.COLOR_WHITE)       
	curses.init_pair(2, curses.COLOR_GREEN, curses.COLOR_BLACK)     
	curses.init_pair(3, curses.COLOR_BLACK, curses.COLOR_BLUE)      
	return ["RED", "GREEN" , "BLUE"]

def color(code,l_color):
	return curses.color_pair(l_color.index(code) + 1)


def random_in_empty_case(what, level, size):
	x = random.randint(1,size-1)
	y = random.randint(1,size-1)            
	while level[y][x] != " ":
		x = random.randint(1,size-1)
		y = random.randint(1,size-1)    
	level[y]= level[y][:x]+str(what)+level[y][x+1:]
	return x, y

def generate_level(n):  
	level= []       
	ligne_start_end = "+" + "=" * n + "+"
	level.append(str(ligne_start_end))
	for i in range(0,n):
		char="|"  
		for j in range(0,n):
			l=random.randint(0,2)
			if i%2 == 0:
				if l == 1:
					char=char+"O"
				else:
					char=char+" "
			else:
				if l == 1:
					char=char+"O"
				else:
					char=char+" "
		char=char+"|"   
		level.append(str(char))
	level.append(str(ligne_start_end))
		
	fic = open ("level_" + str(n) + ".txt", "w")
	for ligne in level:
		ligne = re.sub(r' -'," +", ligne)
		ligne = re.sub(r'- ',"+ ", ligne)
		fic.write(ligne + "\n")
	fic.close
	return level

def next_level(fenetre,n,perso,tresor,monstre):
	global score
	fenetre.destroy()
	
	fenetre2 = Tk()
	nb_tresor = int((n+1) * (n+1) / 10 )
	 
	my_level= generate_level(n+1)

	x,y = random_in_empty_case( perso, my_level, n+1)
	a,o = random_in_empty_case( monstre, my_level, n+1)
	for i in range(nb_tresor):
		random_in_empty_case(tresor, my_level, n+1)


	(canvas, sprite_perso,sprite_monstre,sprite_level,sprite_score,photos)=affichage_tk(fenetre2,my_level,perso,monstre,tresor, size_sprite)
	init_touches(fenetre2, canvas, my_level,[x,y],[a,o], perso,monstre, tresor,sprite_perso,sprite_monstre,sprite_level,sprite_score)
	fenetre2.mainloop()
	
def check_deplacement_m(event,fenetre,level, pos,pos_monstre, deplacement,perso,monstre, tresor, canvas, sprite_perso,sprite_monstre,sprite_level,sprite_score):
	global score
	choix=str(h)+str(b)+str(g)+str(d)
	nl,bonus=check_deplacement(level, pos, deplacement, perso,tresor,canvas,sprite_perso,sprite_level)
	score+=bonus
	canvas.itemconfig(sprite_score, text="Votre score est="+str(score))
	
	if str(nl) == "0":
		check_deplacement(level, pos_monstre,random.choice(choix),monstre,tresor,canvas,sprite_monstre,sprite_level)
	else:
		print ("next level"+str(len(level[0])-2))
		next_level(fenetre,len(level[0])-2,perso,tresor,monstre)
		

def check_deplacement(level, pos, deplacement,perso, tresor, canvas, sprite_perso,sprite_level):
	v = 0
	bonus = 0
	x , y = [pos[0], pos[1]]
	if deplacement == str(h):
		if level[y-1][x] == " " or level[y-1][x] == tresor :
			if level[y-1][x] == tresor:
				bonus = 10
				canvas.delete(sprite_level[y-1][x])
			level[y]=level[y][:x]+" "+level[y][x+1:]                        
			level[y-1]=level[y-1][:x]+perso+level[y-1][x+1:]
			y = y-1
		elif  level[y-1][x] == "=":
			v = 1   
	if deplacement == str(b):
		if level[y+1][x] == " " or level[y+1][x] == tresor :
			if level[y+1][x] == tresor:
				bonus = 10
				canvas.delete(sprite_level[y+1][x])
			level[y]=level[y][:x]+" "+level[y][x+1:]
			level[y+1]=level[y+1][:x]+perso+level[y+1][x+1:]
			y = y+1

		elif  level[y+1][x] == "=":
			v = 1
	if deplacement == str(d):
		if level[y][x+1] == " " or level[y][x+1] == tresor :
			if level[y][x+1] == tresor:
				bonus = 10
				canvas.delete(sprite_level[y][x+1])
			level[y]=level[y][:x]+" "+perso+level[y][x+2:]
			x = x+1
		elif  level[y][x+1] == "|" :
			if level[y][1] == " " or level[y][1] == tresor:
				
				
				if level[y][1] == tresor:
					bonus = 10
					canvas.delete(sprite_level[y][1])
				level[y]=level[y][0]+perso+level[y][2:x]+" "+level[y][x+1]
				x = 1
				
			#v = 1
	if deplacement == str(g):
		if level[y][x-1] == " " or level[y][x-1] == tresor :
			if level[y][x-1] == tresor:
				bonus = 10
				canvas.delete(sprite_level[y][x-1])
			level[y]=level[y][:x-1]+perso+" "+level[y][x+1:]
			x = x -1

		elif  level[y][x-1] == "|":
			if level[y][len(level[y])-2] == " " or level[y][len(level[y])-2] == "T":
				level[y]=level[y][0]+" "+level[y][2:len(level[y])-2]+"X"+level[y][len(level[y])-1]
				x = len(level[y])-2
				if level[y][len(level[y])-2] == tresor:    
					bonus = 10
					canvas.delete(sprite_level[y][len(level[y])-2])
			#v = 1
	del pos[0]
	del pos[0]
	pos.append(x)
	pos.append(y)       
	canvas.coords(sprite_perso, x*size_sprite , y*size_sprite)
	return v, bonus

def destroy(event, fenetre):
	fenetre.destroy()
	
def affichage(win, my_level,perso, x,y, score):
	coul = init_colors()
	i = 0
	
	for ligne in my_level:
		win.addstr(i+1,1,ligne)
		if i == y :
			win.addstr(i+1, x+1,  perso ,color("RED",coul)  )
		i+=1
	win.addstr(i+3,1,"Votre score est "+str(score)) 
	win.addstr(i+4,1,"Level : "+str(i-4))   
	return None


	
def affichage_tk(fenetre,my_level,perso,monstre,tresor, size_sprite):

	fenetre.title("Level:"+str(len(my_level[0])-4)+" SCORE:"+str(score))
	
	can = Canvas(fenetre, width = len(my_level[0])*size_sprite , height = len(my_level[0])*size_sprite+60 )
	photo_perso =  PhotoImage(file="images/small_perso.gif")
	photo_monstre =  PhotoImage(file="images/small_monstre1.gif")
	photo_wall =  PhotoImage(file="images/wall.gif")
	photo_tresor =  PhotoImage(file="images/tresor.gif")
	photo_open =  PhotoImage(file="images/open.gif")                        
	ord=0
	

	sprite=[]
	
	for ligne in my_level:
		abs=0
		spriteligne=[]
		for c in ligne:
						
			if c == perso:
				x=abs
				y=ord
				spriteligne.append(can.create_image(abs*size_sprite,ord*size_sprite, anchor = NW , image = photo_open))
			elif c == monstre:
				a=abs
				o=ord
				spriteligne.append(can.create_image(abs*size_sprite,ord*size_sprite, anchor = NW , image = photo_open))
			elif c == tresor:
				can.create_image(abs*size_sprite,ord*size_sprite, anchor = NW , image = photo_open)
				spriteligne.append(can.create_image(abs*size_sprite,ord*size_sprite, anchor = NW , image = photo_tresor))
				
			elif c == " ":
				spriteligne.append(can.create_image(abs*size_sprite,ord*size_sprite, anchor = NW , image = photo_open)  )
				
			else:
				spriteligne.append(can.create_image(abs*size_sprite,ord*size_sprite, anchor = NW , image = photo_wall) )
			       
			abs+=1
		ord+=1
		sprite.append(spriteligne)
	sprite_monstre = can.create_image(a*size_sprite,o*size_sprite, anchor = NW , image = photo_monstre)	
	sprite_perso = can.create_image(x*size_sprite,y*size_sprite, anchor = NW , image = photo_perso)
	sprite_score = can.create_text(10, len(my_level[0])*size_sprite+5, anchor="nw")

	can.itemconfig(sprite_score, text="Votre score est "+str(score))

	can.pack()
	  
	return (can , sprite_perso,sprite_monstre, sprite,sprite_score,
		{
		"perso":photo_perso,
		"monstre":photo_monstre,
		"wall":photo_wall,
		"open":photo_open,
		"tresor":photo_tresor})

def init_touches(fenetre, canvas, my_level,pos_perso,pos_monstre, perso,monstre, tresor,sprite_perso,sprite_monstre,sprite_level,sprite_score):
	global score
	fenetre.bind("<Right>", lambda event,fen = fenetre , can = canvas, l = my_level, pos = pos_perso, pos_m = pos_monstre , 
		     t=tresor ,  p=perso, m=monstre, sp = sprite_perso,sm = sprite_monstre,
		     sl =sprite_level,sc=sprite_score : check_deplacement_m(event,fen,l, pos,pos_m, str(d) ,p,m,t, can, sp,sm,sl,sc))
	fenetre.bind("<Left>", lambda event,fen = fenetre , can = canvas, l = my_level, pos = pos_perso,pos_m = pos_monstre,
		     t=tresor ,  p=perso, m=monstre,sp = sprite_perso,sm = sprite_monstre,
		     sl =sprite_level,sc=sprite_score : check_deplacement_m(event,fen, l, pos,pos_m,str(g) ,p,m, t, can, sp,sm,sl,sc))
	fenetre.bind("<Up>", lambda event,fen = fenetre , can = canvas, l = my_level, pos = pos_perso,pos_m = pos_monstre,
		     t=tresor ,  p=perso,m=monstre, sp = sprite_perso,sm = sprite_monstre,
		     sl =sprite_level,sc=sprite_score : check_deplacement_m(event,fen, l, pos,pos_m,str(h) ,p,m, t, can, sp,sm,sl,sc))
	fenetre.bind("<Down>", lambda event,fen = fenetre , can = canvas, l = my_level, pos = pos_perso,pos_m = pos_monstre,
		     t=tresor ,  p=perso,m=monstre, sp = sprite_perso,sm = sprite_monstre,
		     sl =sprite_level,sc=sprite_score : check_deplacement_m(event,fen, l, pos,pos_m,str(b) ,p,m, t, can, sp,sm,sl,sc))
	fenetre.bind("<Escape>", lambda event, fen = fenetre : destroy(event, fen))

def main():
# parse command line options
	if len(sys.argv) != 2:
		print("Il manque le niveau. Du coup on part du level 1")
		n = int(3)
	else:
		n = int(sys.argv[1])
		if n < 3:
			print("Le niveau donnée n'est pas pertinent. Du coup on part du level 1")
			n = int(3)
	perso = "X"
	tresor = "T"
	monstre = "%"
	global score
	score = 0
	
	fenetre = Tk()
	       
		
	
	nb_tresor = int(n * n / 10 )        
	my_level= generate_level(n)

	x,y = random_in_empty_case( perso, my_level, n)
	a,o = random_in_empty_case( monstre, my_level, n)
	for i in range(nb_tresor):
		random_in_empty_case(tresor, my_level, n)


	(canvas, sprite_perso,sprite_monstre,sprite_level,sprite_score,photos)=affichage_tk(fenetre,my_level,perso,monstre,tresor, size_sprite)
	init_touches(fenetre, canvas, my_level,[x,y],[a,o], perso,monstre, tresor,sprite_perso,sprite_monstre,sprite_level,sprite_score)
	fenetre.mainloop()
						

	



if __name__ == "__main__":
    main()              
