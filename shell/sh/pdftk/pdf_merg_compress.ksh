cmd="pdftk "

for i in `ls  ${1}*`
do
cmd=$(echo $cmd $i " ")
done
cmd=$(echo $cmd " cat output "${1}"_nc.pdf") 
$cmd
gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile=${1}.pdf ${1}_nc.pdf     
