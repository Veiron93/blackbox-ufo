// окончание слова в зависимости от количества
function endingWord(words, number){ 
    let result = ((number % 100 > 4 && number % 100 < 20) ? 2 : [2, 0, 1, 1, 1, 2][(number % 10 < 5) ? Math.abs(number) % 10 : 5])
    return words[result];
}
