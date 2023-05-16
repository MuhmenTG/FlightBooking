import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment.development';
import { BookingResponse } from '../_models/Employees/Agent/BookingResponse';
import { HttpService } from './http.service';

@Injectable({
  providedIn: 'root'
})
export class BookingService extends HttpService {
  private apiUrl = environment.apiUrl + "/booking";

  getBookings(): Observable<BookingResponse[]> {
    return this.http.get<BookingResponse[]>(this.apiUrl + "/getAllBookings", this.httpOptions);
  }

  getBooking(bookingReference: string): Observable<BookingResponse> {
    return this.http.get<BookingResponse>(this.apiUrl + "/retrieveBooking/" + bookingReference, this.httpOptions);
  }

  updateHotelInfo(bookingReference: string): Observable<BookingResponse> {
    return this.http.get<BookingResponse>(this.apiUrl + "/updateHotelGuestInfo/" + bookingReference, this.httpOptions);
  }
  
  sendEnquiry(): Observable<void> {
    return this.http.get<void>(this.apiUrl + "/sendEnquirySupport", this.httpOptions);
  }
}
