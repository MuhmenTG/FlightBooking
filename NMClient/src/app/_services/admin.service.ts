import { Injectable } from '@angular/core';
import { environment } from 'src/environments/environment.development';
import { AccountsResponse } from '../_models/Employees/AccountsResponse';
import { Observable } from 'rxjs';
import { HttpService } from './http.service';
import { FAQS } from '../_models/FAQS';
import { AccountRequest } from '../_models/Employees/AccountRequest';
import { AccountResponse } from '../_models/Employees/AccountResponse';

@Injectable({
  providedIn: 'root'
})
export class AdminService extends HttpService {
  private apiUrl = environment.apiUrl + "/admin";

  // Accounts
  createAccount(body: AccountRequest): Observable<AccountResponse> {
    return this.http.post<AccountResponse>(this.apiUrl + "/createAgent", JSON.stringify(body), this.httpOptionsAccount);
  }

  editAgent(body: AccountRequest): Observable<AccountResponse> {
    return this.http.post<AccountResponse>(this.apiUrl + "/editAgentDetails", JSON.stringify(body), this.httpOptionsAccount);
  }

  getAccountDetails(id: number): Observable<AccountResponse> {
    return this.http.get<AccountResponse>(this.apiUrl + "/getSpecificAgentDetails/" + id);
  }

  getListOfAccounts(): Observable<AccountsResponse> {
    return this.http.get<AccountsResponse>(this.apiUrl + "/showListOfTravelAgents", this.httpOptionsAccount);
  }

  deactivateOrActivateAccount(id: number): Observable<AccountResponse> {
    return this.http.put<AccountResponse>(this.apiUrl + "/setAgentAccountToDeactiveOrActive/" + id, JSON.stringify(id), this.httpOptionsAccount);
  }

  resetAccountPassword(id: number): Observable<AccountResponse> {
    return this.http.post<AccountResponse>(this.apiUrl + "/resetAgentPassword", JSON.stringify(id), this.httpOptions);
  }

  // FAQ
  createFAQ(body: FAQS): Observable<void> {
    return this.http.post<void>(this.apiUrl + "/createNewFaq", JSON.stringify(body), this.httpOptions);
  }

  editFAQ(id: number, body: FAQS): Observable<void> {
    return this.http.put<void>(this.apiUrl + "/editFaq/" + id, JSON.stringify(body), this.httpOptions);
  }

  removeFAQ(id: number): Observable<void> {
    return this.http.delete<void>(this.apiUrl + "/removeFaq/" + id, this.httpOptions);
  }

  getFAQ(id: number): Observable<FAQS> {
    return this.http.get<FAQS>(this.apiUrl + "/getSpecificFaq/" + id);
  }

  getAllFAQ(): Observable<FAQS[]> {
    return this.http.get<FAQS[]>(this.apiUrl + "/getAllfaq")
  }
}
