#include <string>
#include <fstream>
#include <sstream>
#include <stdio.h>
#include <iostream>
#include <stdlib.h>
#include <stdexcept>
#include <boost/regex.hpp>
#include <boost/algorithm/string.hpp>
#include <boost/xpressive/xpressive.hpp>
#include <boost/filesystem/detail/utf8_codecvt_facet.hpp>
using namespace std;

inline ifstream& read_line(ifstream& file, string& line, bool trim = true) { std::getline(file, line); if(trim) boost::algorithm::trim(line); return file; }
inline void close_array(stringstream& ss, bool auto_cout = true, bool auto_dispose = true) { if(!ss.str().length()) return; ss<<"\t\t)\n\t),"<<endl; if(auto_cout) cout<<ss.str(); if(auto_dispose) ss.str("");  }
inline std::string regex_escape(std::string text){
    const static boost::xpressive::sregex re_escape_text = boost::xpressive::sregex::compile("([\"])");
    text = boost::xpressive::regex_replace( text, re_escape_text, std::string("\\$1") );
    return text;
}
int main()
{
    string line;
    ifstream file("../eil.txt");
    std::locale old_locale;
    std::locale utf8_locale(old_locale, new boost::filesystem::detail::utf8_codecvt_facet);
    file.imbue(utf8_locale);
    ofstream out("../data.php");
    out.imbue(utf8_locale);
    cout.imbue(utf8_locale);
    streambuf* old_cout_buf = cout.rdbuf();
    std::cout.rdbuf(out.rdbuf());

    if(!file.is_open()) { throw std::runtime_error("Could not open book's input file!"); }

    cout<<"<?php"<<endl;
    cout<<"/**"<<endl
        <<"*"<<endl
        <<"* If the contents do not display right, please convert data from `windows-1250` to `utf8` encoding."<<endl
        <<"*"<<endl
        <<"*/"<<endl;

    read_line(file, line);
    cout<<"$book_author = \""<<line<<"\";"<<endl;

    read_line(file, line);
    cout<<"$book_title = \""<<line<<"\";"<<endl;

    cout<<"$book_content = array("<<endl;

    uint counter = 0;
    stringstream ss;

    for( ; read_line(file, line); ) {
        if(!line.length()) continue;
        boost::cmatch what;
        if(boost::regex_match(line.c_str(), what, boost::regex("^\\d+$"))) {
            if(counter != 0) { close_array(ss); counter = 0; }
            ss<<"\tarray(\n\t\t\"chap\" => "<< regex_escape(line) <<","<<endl;
            counter++;
        } else if(counter == 1 && boost::regex_match(line.c_str(), what, boost::regex("^[\\w ?'-]+$"))) {
            ss<<"\t\t\"title\" => \""<< regex_escape(line) <<"\","<<endl;
            counter++;
        } else if(boost::regex_match(line.c_str(), what, boost::regex("^(\\d+)\\.\\s*(.+)$"))) {
            if(counter == 2) ss<<"\t\t\"sections\" => array("<<endl;
            ss<<"\t\t\tarray(\n\t\t\t\t\"sec\" => " << what[1] << "," << endl;
            ss<<"\t\t\t\t\"content\" => \""<< regex_escape(what[2]) << "\"\n\t\t\t)," << endl;
            counter++;
        }
    }
    close_array(ss);
    cout<<");";
    cout.rdbuf(old_cout_buf);
    cout<<"DONE!";
    return 0;
}