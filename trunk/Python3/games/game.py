#!/usr/local/bin/python3
import random
import sys
import getopt
import re
from optparse import OptionParser
#import curses

h = 8
b = 2
g = 4
d = 6

options = OptionParser(usage='%prog server [options]', description='Test for SSL heartbeat vulnerability (CVE-2014-0160)')
options.add_option('-p', '--port', type='int', default=443, help='TCP port to test (default: 443)')

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
#               print(ligne)
                fic.write(ligne + "\n")
        fic.close
        return level

def check_deplacement(level, x, y, deplacement):
        v = 0
        bonus = 0
        if deplacement == str(h):
                if level[y-1][x] == " " or level[y-1][x] == "T" :
                        if level[y-1][x] == "T":
                                bonus = 10
                        level[y]=level[y][:x]+" "+level[y][x+1:]
                        level[y-1]=level[y-1][:x]+"X"+level[y-1][x+1:]
                        y = y-1
                elif  level[y-1][x] == "=":
                        print ("VICTOIRE!!!!")
                        v = 1
                else:
                        print ( "deplacement impossible")
        if deplacement == str(b):
                if level[y+1][x] == " " or level[y+1][x] == "T" :
                        if level[y+1][x] == "T":
                                bonus = 10
                        level[y]=level[y][:x]+" "+level[y][x+1:]
                        level[y+1]=level[y+1][:x]+"X"+level[y+1][x+1:]
                        y = y+1

                elif  level[y+1][x] == "=":
                        print ("VICTOIRE!!!!")
                        v = 1
                else:
                        print ( "deplacement impossible")
        if deplacement == str(d):
                if level[y][x+1] == " " or level[y][x+1] == "T" :
                        if level[y][x+1] == "T":
                                bonus = 10
                        level[y]=level[y][:x]+" X"+level[y][x+2:]
                        x = x+1
                elif  level[y][x+1] == "|":
                        print ("VICTOIRE!!!!")
                        v = 1
                else:
                        print ( "deplacement impossible")
        if deplacement == str(g):
                if level[y][x-1] == " " or level[y][x-1] == "T":
                        if level[y][x-1] == "T":
                                bonus = 10
                        level[y]=level[y][:x-1]+"X "+level[y][x+1:]
                        x = x -1

                elif  level[y][x-1] == "|":
                        print ("VICTOIRE!!!!")
                        v = 1
                else:
                        print ( "deplacement impossible")
        return level, x, y, v, bonus
def main():
# parse command line options
        if len(sys.argv) != 2:
                print("Il manque le niveau. Du coup on part du level 1")
                n = int(3)
        else:
                n = int(sys.argv[1])
                if n < 3:
                        print("Le niveau donnÃ©e n'est pas pertinent. Du coup on part du level 1")
                        n = int(3)
        perso = "X"
        score = 0

        while (True):

 #               stdscr = curses.initscr()
 #               curses.noecho()
 #               curses.cbreak()
                
                nb_tresor = int(n * n / 10 )
                v = 0
                my_level= generate_level(n)
        #deplacement = input(" Saisissez un dÃ©placement (H / B / G / D) : ")
                x,y = random_in_empty_case( perso, my_level, n)
                for i in range(nb_tresor):
                        random_in_empty_case("T", my_level, n)
                while (v == 0 ):
                        print ("votre score est "+str(score))
                        for ligne in my_level:
                                print(ligne)
                        deplacement = input(" Saisissez un dÃ©placement (h="+str(h)+"/b="+str(2)+"/g="+str(4)+"/d="+str(6)+") ou quittez (q) : ")
                        if deplacement != "q":
                                my_level, x, y, v, bonus = check_deplacement(my_level, x, y, deplacement)
                                score+=bonus
                        else:
                                return
        #print (deplacement)
                score+=n
                n+=1
                print ("Level : "+str(n-2))
if __name__ == "__main__":
    main()
