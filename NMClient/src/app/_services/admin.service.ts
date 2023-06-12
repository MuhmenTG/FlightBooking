import { Injectable } from '@angular/core';
import { environment } from 'src/environments/environment.development';
import { AccountResponse } from '../_models/Employees/AccountResponse';
import { Observable } from 'rxjs';
import { HttpService } from './http.service';
import { FAQ } from '../_models/FAQ';
import { AccountRequest } from '../_models/Employees/AccountRequest';

@Injectable({
  providedIn: 'root'
})
export class AdminService extends HttpService{
  private apiUrl = environment.apiUrl + "/admin";

  // Accounts
  createAccount(body: AccountRequest): Observable<AccountResponse> {
    return this.http.post<AccountResponse>(this.apiUrl + "/createAgent", JSON.stringify(body), this.httpOptions);
  }

  editAgent(id: number, body: AccountRequest): Observable<AccountResponse> {
    return this.http.put<AccountResponse>(this.apiUrl + "/editAgentDetails/" + id, JSON.stringify(body), this.httpOptions);
  }

  getAccountDetails(id: number): Observable<AccountResponse> {
    return this.http.get<AccountResponse>(this.apiUrl + "/getSpecificAgentDetails/" + id);
  }

  getListOfAccounts(): Observable<AccountResponse[]> {
    return this.http.get<AccountResponse[]>(this.apiUrl + "/showListOfAgent");
  }

  deactivateAccount(id: number): Observable<AccountResponse> {
    return this.http.post<AccountResponse>(this.apiUrl + "/setAgentAccountToDeactive/" + id, this.httpOptions);
  }

  resetAccountPassword(id: number): Observable<AccountResponse> {
    return this.http.post<AccountResponse>(this.apiUrl + "/resetAgentPassword", JSON.stringify(id), this.httpOptions);
  }


  // FAQ
  createFAQ(body: FAQ): Observable<void> {
    return this.http.post<void>(this.apiUrl + "/createNewFaq", JSON.stringify(body), this.httpOptions);
  }

  editFAQ(id: number, body: FAQ): Observable<void> {
    return this.http.put<void>(this.apiUrl + "/editFaq/" + id, JSON.stringify(body), this.httpOptions);
  }

  removeFAQ(id: number): Observable<void> {
    return this.http.delete<void>(this.apiUrl + "/removeFaq/" + id, this.httpOptions);
  }

  getFAQ(id: number): Observable<FAQ> {
    return this.http.get<FAQ>(this.apiUrl + "/getSpecificFaq/" + id);
  }

  getAllFAQ(): Observable<FAQ[]> {
    return this.http.get<FAQ[]>(this.apiUrl + "/getAllfaq")
  }
}
