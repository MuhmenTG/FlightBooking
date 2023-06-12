import { Injectable } from '@angular/core';
import { HttpService } from './http.service';
import { environment } from 'src/environments/environment.development';
import { Observable } from 'rxjs';
import { EnquiryResponse } from '../_models/Enquiries/EnquiryResponse';

@Injectable({
  providedIn: 'root'
})
export class AgentService extends HttpService {
  private apiUrl = environment.apiUrl + "/travelAgent";

  // getAllFlightBookings(): Observable<PH> {
  //   return this.http.get<PH>(this.apiUrl + "/getAllFlightBookings");
  // }

  // getAllHotelBookings(): Observable<PH> {
  //   return this.http.get<PH>(this.apiUrl + "/getAllHotelBookings");
  // }

  // getBooking(id: number): Observable<PH> {
  //   return this.http.get<PH>(this.apiUrl + "/getBooking/" + id);
  // }

  // cancelHotelBooking(id: number): Observable<AccountResponse> {
  //   return this.http.put<AccountResponse>(this.apiUrl + "/cancelHotel/" + id, this.httpOptions);
  // }

  // cancelFlightBooking(id: number): Observable<AccountResponse> {
  //   return this.http.put<AccountResponse>(this.apiUrl + "/cancelFlight/" + id, this.httpOptions);
  // }

  // sendBookingToCustomer(): Observable<AccountResponse> {
  //   return this.http.post<AccountResponse>(this.apiUrl + "/sendBooking", this.httpOptions);
  // }

  // answerEnquiry(): Observable<AccountResponse> {
  //   return this.http.post<AccountResponse>(this.apiUrl + "/answerUserEnquiry", this.httpOptions);
  // }

  // setEnquiryStatus(id: number): Observable<AccountResponse> {
  //   return this.http.post<AccountResponse>(this.apiUrl + "/setUserEnquiryStatus/" + id, this.httpOptions);
  // }

  // deleteEnquiryStatus(id: number): Observable<AccountResponse> {
  //   return this.http.delete<AccountResponse>(this.apiUrl + "/removeUserEnquiry/" + id);
  // }

  // getAllEnquiries(): Observable<EnquiryResponse[]> {
  //   return this.http.get<EnquiryResponse[]>(this.apiUrl + "/getAllUserEnquries");
  // }

  // getSpecificEnquiry(id: number): Observable<EnquiryResponse> {
  //   return this.http.get<EnquiryResponse>(this.apiUrl + "/getSpecificUserEnquiry/" + id);
  // }

  // editAccount(): Observable<PH> {
  //   return this.http.post<PH>(this.apiUrl + "/editAgentDetails", this.httpOptions);
  // }
}
