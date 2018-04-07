#!/bin/sh -e
clear

Miner Editado por: @ScripterOS

echo "			\033[41;1;37m Instalador \033[0m "	
sleep 1
echo "\033[44;1;37m Selecione a Moeda:     \033[0m "

corPadrao="\033[0m"
preto="\033[0;30m"
vermelho="\033[0;31m"
verde="\033[0;32m"
marrom="\033[0;33m"
azul="\033[0;34m"
purple="\033[0;35m"
cyan="\033[0;36m"
cinzaClaro="\033[0;37m"
pretoCinza="\033[1;30m"
vermelhoClaro="\033[1;31m"
verdeClaro="\033[1;32m"
amarelo="\033[1;33m"
azulClaro="\033[1;34m"
purpleClaro="\033[1;35m"
cyanClaro="\033[1;36m"
branco="\033[1;37m"




sleep 1.5s
echo "$amarelo [1] DOGECOIN"
read moeda


if [ "$moeda" = "1" ]
then
sleep 1
echo "\033[44;1;37m Baixando....     \033[0m "
sleep 0.5s
wget https://github.com/scripteros/MinerScritpDOGE/blob/master/DOGEMINE.sh
echo "\033[44;1;37m Instalado com Sucesso!     \033[0m "

echo "Para Ultilizar o Minerasor digite Assim:\n"
echo "$verde sudo sh DOGEMINE.sh $amarelo CARTEIRA DOGECOIN\033[0m"
fi